# Testing Event Sourcing

## Table of Content

- [Testing Aggregates](#testing-aggregates)
  - [Design](#testing-aggregates-design)
  - [Implementation](#testing-aggregates-implementation)
    - [OpportunityTest](#opportunitytest)
- [Testing Event Sourcing scenarios](#testing-event-sourcing-scenarios)
  - [Design](#testing-design)
  - [Implementation](#testing-implementation)
    - [TimelineProcessorForVisitAppointmentActivityTest](#timelineprocessorforvisitappointmentactivitytest)
      - [testSchedulingVisitAppointment](#testschedulingvisitappointment)
      - [scheduleVisitAppointmentActivity](#schedulevisitappointmentactivity)
      - [assertTimeline](#asserttimeline)
      - [Support methods](#support-methods)
        - [prepareApiLeadInquiry](#prepareapileadinquiry)
        - [sendScheduleVisitAppointmentActivity](#sendschedulevisitappointmentactivity)
        - [findTimelineByLeadId](#findtimelinebyleadid)

## Testing Aggregates

### Testing Aggregates Design

To test complex busniess logic for an aggregate in an Event Sourcing application, we use Unit Tests.
These tests rely *not* on API calls or DB access. You have to mock any dependency.

Let us assume that we want to test that an opportunity cannot be deactivated if a reservation was already started:

- an opportunity was claimed by an agent
- an agent starts a reservation for this opportunity
- it is not possible to deactivate the opportunity

Assume you have the following method in your aggregate, which should be tested.

```php
    public function deactivate(
        DeactivationReasonList $reasonList,
        DeactivatedAt $deactivatedAt,
        DeactivationReasonNote $note = null
    ): void {
        if ($this->state->reservationStatus()->isInReservation()) {
            throw LeadDeactivationFailed::reservationAlreadyStarted($this->state->leadId());
        }

        // record event LeadDeactivated
    }
```

To test this, the following steps are required in the Unit Test:

- **Step 1**: Create an opportunity (lead)

  - Check that the *OpportunityClaimed* domain event has been recorded by the aggregate

- **Step 2**: Track that reservation has started

  - Check that the *ReservationStarted* domain event has been recorded by the aggregate

- **Step 3**: Deactivate opportunity (should throw exception)

  - Check that the exception `OpportunityDeactivationFailed` was thrown

### Testing Aggregates Implementation

To implement a unit test, we suggest the following steps:

- create a PHPUnit test class with the namespace of your tested class in the folder `tests/PHPUnit/Unit`
  - For instance the class `Domain\v1\Opportunity\Model\Opportunity` would lead to `tests/PHPUnit/Unit/Domain/v1/Opportunity/Model/OpportunityTest.php`
- add public methods for each Test where each step *clearly* describes what it does
- creating event(s) and updating the Aggregate
- confirm the updated aggregate State

#### OpportunityTest

To test which domain events are recorded by the aggregate we use the method `popRecordedEvents()`. This method can be called
after each aggregate method or at the end to get all recorded domain events at once.

- create a new opportunity and check recorded domain events
- track reservation started for this opportunity
- define expected exception and deactivate opportunity

```php
namespace Tests\PHPUnit\Unit\Domain\v1\Opportunity\Model;

use PHPUnit\Framework\TestCase;

final class OpportunityTest extends TestCase
{
    /**
     * @covers \Domain\v1\Opportunity\Model\Opportunity::deactivate
     */
    public function testItThrowsExceptionOnOpportunityDeactivationIfReservationWasStarted(): void
    {
        $opportunity = Opportunity::newOpportunityClaimed(...);
        $recordedEvents = $opportunity->popRecordedEvents();

        $this->assertCount(1, $recordedEvents);
        $this->assertInstanceOf(OpportunityClaimed::class, $recordedEvents[0]);
        $this->assertFalse($opportunity->state()->deactivated()->toBool());

        $opportunity->trackReservationStarted();
        $recordedEvents = $opportunity->popRecordedEvents();

        $this->assertCount(1, $recordedEvents);
        $this->assertInstanceOf(ReservationStarted::class, $recordedEvents[0]);

        $this->expectException(OpportunityDeactivationFailed::class);
        $opportunity->deactivate(...);
    }
}
```

## Testing Event Sourcing scenarios

### Testing Design

To test a scenario or workflow in an Event Sourcing application, we use Integration Tests.
These tests rely primarily on API calls and DB access.

Let us assume that we want to test a new **Appointment Scheduling** feature managed by the
consultation microservice:

- an agent schedules a visiting appointment for an opportunity with a prospect
- the appointment is displayed in the *todo overview* as a reminder
- the appointment is displayed in the *opportunity timeline*

This helps the agent know what he/she has to do or what has been done in the past.
This feature relies on three aggregates:

- Opportunity
- VisitAppointmentActivity
- Timeline

To test this feature, the following steps are required in the Scenario Test:

- **Step 1**: Prepare an opportunity (lead)

  - Check that the opportunity was created successfully
  - Check that the *LeadClaimed* domain event has been recorded in the *Opportunity* aggregate stream

- **Step 2**: Create the scheduled visit appointment

  - Send an HTTP request to an API endpoint to create the visit appointment
  - Check that the *VisitAppointmentScheduled* domain event has been recorded
  - Check that the *VisitAppointmentActivity* aggregate state has been updated

- **Step 3**: Rebuild the *todo overview* and *opportunity timeline* projections

  - Run all projections, because both *overview* and the *timeline* projections listen to the *VisitAppointmentActivity* stream
  - Check that the *todo overview* contains the new entry
  - Check that the *VisitAppointmentScheduledEntryAdded* domain event has been recorded
  - Check that the *Timeline* aggregate state has been updated

### Testing Implementation

To implement a scenario test, we suggest the following steps:

- create a PHPUnit test Class in the folder `tests/PHPUnit/Integration/Scenario` with a meaningful Scenario name
- add public methods for each Test where each step *clearly* describes what it contributes to the scenario
- add private/protected methods to support all necessary steps such as
- preparing API client call(s)
- preparing Auth Header
- creating event(s) and updating the Aggregate(s)
- confirm the updated aggregate State

#### TimelineProcessorForVisitAppointmentActivityTest

In this case, we declare data fixtures using constants at the top of the class.

```php
use \Tests\PHPUnit\IntegrationTestCase;

final class TimelineProcessorForVisitAppointmentActivityTest extends IntegrationTestCase
{
    // Use constants for the test data like ids
    private const PROJECT_ID = 999999;
    private const PROJECT_NAME = 'test project name';
    private const BUYER_ID = '9572d744-90e2-4944-9351-336b5f4774e0';
    private const AGENT_ID = '0da507e1-9f8b-4776-9d7c-5de1d89a7e64';

  // ... see content below
}
```

##### testSchedulingVisitAppointment

- prepares necessary data and mocks external API Client call
- loads data from database
- executes appropriate calls
- ensures projections are processed the domain event from above
- invokes PHPUnit assertions

```php
    /**
     * @testdox An agent can schedule a visit appointment
     */
    public function testSchedulingVisitAppointment(): void
    {
        $buyerId = BuyerId::fromString(self::BUYER_ID);
        $projectId = ProjectId::fromInt(self::PROJECT_ID);

        $this->prepareBuyerAndProject();
        $this->prepareApiLeadInquiry($buyerId, $projectId);
        $this->prepareLead();

        $lead = $this->findLeadByProjectIdAndBuyerId($projectId, $buyerId);
        $leadId = $lead->leadId()->toString();

        $this->scheduleVisitAppointmentActivity($leadId);

        $this->runAllProjections();

        $this->assertTimeline($leadId);
    }
```

##### scheduleVisitAppointmentActivity

- calls your internal service API endpoint to schedule a visit appointment
- checks if event has been recorded
- if needed: checks aggregate state

```php
    private function scheduleVisitAppointmentActivity(string $leadId): void
    {
        // call your service API endpoint to schedule a visit appointment
        $visitAppointmentActivityId = $this->sendScheduleVisitAppointmentActivity(
            $leadId,
            self::AGENT_ID
        );

        // check if event was recorded
        $this->canSeeEventInAggregateStream(
            VisitAppointmentActivityRepository::STREAM_NAME,
            VisitAppointmentActivityRepository::AGGREGATE_TYPE,
            $visitAppointmentActivityId,
            VisitAppointmentScheduled::NAME,
            [
                VisitAppointmentScheduled::AGENT_ID => self::AGENT_ID,
                /** ... check other domain event payload data too */
            ]
        );

        // make also some aggregate state checks if needed
    }
```

##### assertTimeline

- checks if timeline entry event has been recorded (via projection)
- checks timeline state

```php
 private function assertTimeline(string $leadId): void
    {
        // check if timeline entry event was recorded (via projection)
        $this->canSeeEventInAggregateStream(
            TimelineRepository::STREAM_NAME,
            TimelineRepository::AGGREGATE_TYPE,
            $leadId,
            VisitAppointmentScheduledEntryAdded::NAME
        );

        // check timeline state
        $timeline = $this->findTimelineByLeadId(LeadId::fromString($leadId));

        $this->assertCount(2, $timeline->entries());

        $this->assertTrue(
            $timeline->entries()->last()->type()->equals(Type::fromString(Type::VISIT_APPOINTMENT_SCHEDULED)),
        );
    }
```

##### Support methods

###### prepareApiLeadInquiry

- Mocks the API call for external buyer service via Prophecy

```php
protected function prepareApiLeadInquiry(BuyerId $buyerId, ProjectId $projectId, $inquiries = []): void
{
    $leadConfig = $this->serviceConfig('lead');

    $data = [
        'data' => $inquiries,
    ];

    $apiClient = $this->service(ApiClient::class)->getProphecy();

    $apiClient->get(
        Argument::is(
            $leadConfig['base_url'] . str_replace(
            ['{buyer_id}', '{project_id}'],
            [$buyerId->toString(), $projectId->toInt()],
            InquiryService::URL_LEADS_INQUIRY)
        ),
    )
        ->willReturn($data)->shouldBeCalled();
}
```

###### sendScheduleVisitAppointmentActivity

- Makes the API call for our service to schedule a visit appointment

```php
protected function sendScheduleVisitAppointmentActivity(
    string $leadId,
    string $agentId,
    array $override = []
): string {
    $response = $this->json(
        RequestMethodInterface::METHOD_POST,
        VisitAppointmentRoutes::fullRoute(VisitAppointmentRoutes::VISIT_APPOINTMENT_ACTIVITY_SCHEDULE),
        array_merge([
            ScheduleVisitAppointmentActivity::LEAD_ID => $leadId,
            ScheduleVisitAppointmentActivity::AGENT_ID => $agentId,
            ScheduleVisitAppointmentActivity::LOCATIONS => null,
            ScheduleVisitAppointmentActivity::NOTE => null,
            ScheduleVisitAppointmentActivity::TIMESTAMP => Timestamp::fromDateTime((new \DateTimeImmutable())->add(new \DateInterval('PT1H')))->toString(),
        ], $override),
        $this->authHeader([Scopes::AGENTS_READ_WRITE], $agentId)
    );

    $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $response->status());

    return VisitAppointmentRoutes::idFromLocationHeader($response->headers->get('location'));
}
```

###### findTimelineByLeadId

- Retrieves the updated *Timeline* state given the new lead provided

```php
public function findTimelineByLeadId(LeadId $leadId): Timeline
{
    /** @var TimelineRepositoryContract $timeLineRepository */
    $timeLineRepository = $this->service(TimelineRepositoryContract::class);

    return $timeLineRepository->getState($leadId);
}
```
