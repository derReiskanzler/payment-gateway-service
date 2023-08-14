# AMH new directory structure

This is the `src` folder structure proposed by Allmyhomes team. Please see Jira Ticket: https://allmyhomes.atlassian.net/browse/ATD-29

Allmyhomes aligned to the following directory structure and there is a [full documentation][1]. 

```
src/
├── Application
│   ├── DeactivateOpportunity (Change State Use Case From Api)
│   │   └── Command
│   │   │   ├── DeactivateOpportunity.php
│   │   │   ├── DeactivateOpportunityHandler.php
│   │   │   ├── UndoOpportunityDeactivation.php
│   │   │   └── UndoOpportunityDeactivationHandler.php
│   ├── PopulateTimeline (Change State Use Case From Api Or Event)
│   │   ├── Command
│   │   │   ├── RecordEventInTimeline.php
│   │   │   └── RecordEventInTimelineHandler.php
│   │   └── ProcessManager (consumes one or multiple streams and triggers something new)
│   │   │   ├── CallActivityProcessManager.php (handles call activity events)
│   │   │   └── VisitAppointmentActivityProcessManager.php (handles visit appointment activity events)
│   │   └── Repository (specific repository interfaces needed for this use case)
│   │       └── TimelineRepositoryInterface.php (Read Repository)
│   ├── PopulateAgentTodoOverview (Query Use Case)
│   │   ├── Document
│   │   │   └── AgentTodoOverview.php (to be used in the projector and passed to the repository)     
│   │   ├── Query
│   │   │   ├── GetCurrentAgentTodos.php
│   │   │   └── GetCurrentAgentTodosHandler.php
│   │   ├── Projector (consumes one or multiple streams)
│   │   │   └── PopulateAgentTodoOverview.php (View)
│   │   └── Repository (interfaces)
│   │       └── AgentTodoOverviewRepositoryInterface.php
│   ├── AgentMonitoring (Publish event-sorucing event to Public event Use Case)
│   │   ├── PublicEvent (shared domain events for publishing in shared event store)
│   │   │   └── AgentActivityDetected.php
│   │   └── ProcessManager (publishes domain events to shared event store)
│   │       └── PublishAgentActivity.php
├── Domain
│   ├── CallActivity
│   │   ├── Aggregate
│   │   │   ├── CallActivity.php
│   │   │   └── CallActivityState.php
│   │   ├── DomainService (logic which not fits in aggregate)
│   │   │   ├── GetProjectInformationInterface.php
│   │   │   └── ProspectServiceInterface.php
│   │   ├── Repository
│   │   │   └── CallActivityRepositoryInterface.php
│   │   ├── Event
│   │   │   ├── CallRescheduled.php
│   │   │   └── CallScheduled.php
│   │   ├── Exception (Specific Domain Exception for this aggregate)
│   │   │   └── InvalidCallTimeException.php
│   │   └── ValueObject (specific VOs for this aggregate)
│   │           ├── CanceledBy.php
│   │           ├── CanceledByReason.php
│   │           ├── Note.php
│   │           └── Timestamp.php
│   ├── VisitAppointment
│       ├── Aggregate
│       │   ├── VisitAppointment.php
│       │   └── VisitAppointmentState.php
│       ├── Repository
│       │   └── VisitAppointmentRepositoryInterface.php
│       ├── Event
│       │   ├── DocumentedVisitAppointmentChanged.php
│       │   ├── DocumentedVisitAppointmentDeleted.php
│       │   ├── ScheduledVisitAppointmentCanceled.php
│       │   ├── ScheduledVisitAppointmentChanged.php
│       │   ├── VisitAppointmentDidNotTakePlace.php
│       │   ├── VisitAppointmentDidTakePlace.php
│       │   ├── VisitAppointmentLocationChanged.php
│       │   ├── VisitAppointmentRescheduled.php
│       │   └── VisitAppointmentScheduled.php
│       └── ValueObject (specific VOs for this aggregate)
│           ├── Activity (duplicated like in CallActivity)
│           │   ├── CanceledBy.php
│           │   ├── CanceledByReason.php
│           │   ├── Note.php
│           │   └── Timestamp.php
│           ├── LocationList.php
│           ├── Location.php
│           └── TookPlace.php
│   ├── Context.php (public constants)
│   └── ValueObject (shared value objects like IDs or common VOs)
│   │   ├── CallActivityId.php
│   │   ├── EmailActivityId.php
│   │   ├── OccurredAt.php
│   │   ├── OpportunityId.php
│   │   └── VisitAppointmentActivityId.php
└── Infrastructure
    ├── Inbound
    │   ├── Api (HTTP API gateway via api-unparsed.yml, Laravel specific)
    │   │   ├── Controller
    │   │   │   ├── CallActivityController.php
    │   │   │   └── VisitAppointmentActivityController.php
    │   │   └── Request
    │   │   │   ├── CallActivityRequest.php
    │   │   │   └── VisitAppointmentActivityRequest.php
    │   │   └── Route
    │   │       ├── CallActivity
    │   │       │    └── call_activity.php
    │   │       ├── Scope.php (public constants for access scopes)
    │   │       └── api.php (requires call_activity.php and other dedicated route declarition php files)
    │   ├── Console (CLI gateway, Laravel specific)
    │   │   └── RebuildAggregateStateReadModel.php
    ├── Outbound
    │   ├── Http
    │   │   ├── Client (HTTP clients for external services)
    │   │   │   ├── ProjectInformationService.php
    │   │   │   └── ProspectService.php
    │   └── Repository
    │       ├── Persistence (implementations of Aggregate Writing Repository interface)
    │       │   ├── Prospect
    │               └── ProspectRepository.php
    │       └── Query (implementations of Query Repository Interface)
    │           └── Prospect
    │               └── ProspectQueryRepository.php
    │           └── QueryUseCase
    │               └── UnitListRepository.php
    │               └── UnitListQueryRepository.php
    ├── Stream.php (public constants for streams)
    └── ServiceProvider (Laravel service provider)
        └── LaravelVisitAppointmentActivityProvider.php
```

This `src` structure is represented here as an example. Please check each directory for more information about what should be placed inside. 

[1]: <https://allmyhomes.atlassian.net/wiki/spaces/AC/pages/583008344/How+to+build+EDA+allmyhomes>
