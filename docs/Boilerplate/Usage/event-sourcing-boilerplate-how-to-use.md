# How to use Event Sourcing in Boilerplate v2.0.0

In Boilerplate v2.0.0, we introduced the infrastructure layer of Event Sourcing which includes `Aggregates`, `Immutable State`, `CQRS` and `Prooph MultiModel Store`.

**Important:** Once you start to use `EventSourcing`, you need to migrate to `Postgres` by following this [guide](../Migrations/service-migration-to-eventsourcing.md)

Now let's start to focus on the core of v2.0.0 which is building a complete event sourcing application, we will discuss about the core classes and which classes need to be extended

## Controller

It's obvious that any request to the application will start from `ApplicationLayer->Controller`, that's why we first need to build our controller.

As in the example below:

- We need to extend a new base controller which is `\Application\Core\Http\Controllers\EventSourcingController`
- We need to validate our Request using `Laravel Request Validator`, which is done by using `BuyerRequest->rules()`
- We call method `getCommand` to compose our command by passing `Command Class`, `Command Payload`, `Command Metadata (optional)` and `CommandId (Optional)`
- We call method `getCommandHandler` to compose our command handler by passing `Command Handler Class`
- We call method `$commandHandler->handle($command)` which returns our `Identifier`

```php
class BuyerController extends \Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers\EventSourcingController
{
    public function registerUserAsBuyer(BuyerRequest $request)
    {
        $command = $this->getCommand(MakeVisitAppointment::class, $request, [
           'full_name' => $data['name'],
           'email' => $data['email'],
        ], '3108569b-e474-4b2d-8356-b4da0caf6755');

        $commandHandler = $this->getCommandHandler(MakeVisitAppointmentHandler::class);

        $aggregateId = $commandHandler->handleRegisterUserAsBuyer($command);

        $location = app()->make('Helper')->UrlHelper->buildLocationUrl($request->segments(), $aggregateId);

        return $this->response->created($location);
    }
}
```

Now we are done from our controller method, we need to implement our `Command` and `Command Handler`

### Command

Our command is basically a pure PHP class that is responsible to initialize a state change in our `Aggregate` and in general a command can be accepted or rejected by the `Aggregate`.
The command class should always be `final` class and should be `Immutable` for that we can build our class like below

- We need to create a `Command` inside our `Domain/Service/Commands` Layer
- Our command class is a `final` class and extends `\Domain\v1\Abstractions\AbstractCommand`
- We need to define the command name and has to begin with our Context Name (Boilerplate.) for example
- As our `Command` should be `Immutable` which means that can't be changed once initialized, so we **can't** have any setter methods or any method that change the value of the command properties.
- It's important that we identify the `Identifier` of our `Aggregate` by ourselves and not using `auto_increment` of the database that's why we have this method `buyerId()` which generates `UUID` for our new buyer.
- We need to identify methods that convert the request input to a data type object (Value Objects)

```php
final class RegisterUserAsBuyer extends \Domain\v1\Abstractions\AbstractCommand
{
    private const name = 'Boilerplate.RegisterUserAsBuyer';

    private const BUYER_EMAIL = 'email';

    private const BUYER_FULL_NAME = 'full_name';

    /**
     * @return CommandName
     */
    public function commandName(): CommandName
    {
        return CommandName::fromString(self::name);
    }

    public function buyerId(): BuyerId
    {
        return BuyerId::generate();
    }

    public function buyerEmail(): BuyerEmail
    {
        return BuyerEmail::fromString($this->get(self::BUYER_EMAIL));
    }

    public function buyerFullName(): BuyerFullName
    {
        return BuyerFullName::fromString($this->get(self::BUYER_FULL_NAME));
    }
}
```

## Command Handler

Our Command Handler is basically a service class (what we called before as `Application Service`), also it's `final` class that handle different business state changes to single `Aggregate`

- We create our first `CommandHandler` below `Domain/Service/Services` and implements `CommandHandler` Interface
- We name our class that ends with `Handler`-> `UserAsBuyerHandler`
- We create a method called `handleRegisterUserAsBuyer` which takes our command `RegisterUserAsBuyer` as argument
- We call a business method inside our `Aggregate` then persist our `state` change
- We return `Buyer Id` to return it with our response to the client.

```php
final class UserAsBuyerHandler implements \Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandHandler
{
    /**
     * @var UserAsBuyerRepositoryContract
     */
    private $repository;

    /**
     * UserAsBuyerHandler constructor.
     * @param UserAsBuyerRepositoryContract $repository
     */
    public function __construct(UserAsBuyerRepositoryContract $repository)
    {
        $this->repository = $repository;
    }


    public function handleRegisterUserAsBuyer(RegisterUserAsBuyer $command): AggregateId
    {
        $buyer = Buyer::registerUserAsBuyer(
            $command->buyerId(),
            $command->buyerEmail(),
            $command->buyerFullName()
        );

        $this->repository->save($buyer, $command);

        return $buyer->aggregateId();
    }
}
```

Now it's time to move forward and identify our `Aggregate` and `State` objects

## Aggregate & State

Our `Aggregate` is basically a class that holds our entity state and is the only source that is allowed to change the state of our object.

It contains all our business logic, validate against them then it either accepts or rejects the command.

- Our `Aggregate` class has to be final as well and extends `AbstractAggregateRoot` and created in `Domain/Service`
- Each `Aggregate` class has it's own `Immutable` state which is important rule.
- We create our method to register the buyer as well as `When` method which is responsible to update the state from an occurred event
- Our `State` class has to be `Immutable` and has to implemente `ImmutableState`

```php
final class Buyer extends \Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\AbstractAggregateRoot
{
    /**
     * @var State
     */
    protected $state;

    public static function registerUserAsBuyer(
        BuyerId $buyerId,
        buyerEmail $buyerEmail,
        buyerFullName $buyerFullName
    ): self {
        $self = new self();
        $self->recordThat(UserAsBuyerRegistered::fromRecordData([
            UserAsBuyerRegistered::BUYER_ID => $buyerId,
            UserAsBuyerRegistered::BYER_EMAIL => $buyerEmail,
            UserAsBuyerRegistered::BUYER_FULL_NAME => $buyerFullName,
        ]));

        return $self;
    }

    protected function whenUserAsBuyerRegistered(UserAsBuyerRegistered $event): State
    {
        return State::fromRecordData([
            State::BUYER_ID => $event->buyerId(),
            State::BYER_EMAIL => $event->buyerEmail(),
            State::BUYER_FULL_NAME => $event->buyerFullName(),
        ]);
    }

    /**
     * @return AggregateId
     */
    public function aggregateId(): AggregateId
    {
        return AggregateId::fromValueObject($this->state->buyerId());
    }
}

final class State implements ImmutableState
{
    use ImmutableRecordLogicTrait;

    public const BUYER_ID = 'buyerId';
    public const BUYER_EMAIL = 'buyerEmail';
    public const BUYER_FULL_NAME = 'buyerFullName';

    /**
     * @var BuyerId
     */
    private $buyerId;

    /**
     * @var BuyerEmail
     */
    private $buyerEmail;

    /**
     * @var BuyerFullName
     */
    private $buyerFullName;

    /**
     * @return BuyerId
     */
    public function buyerId(): BuyerId
    {
        return $this->buyerId;
    }

    /**
     * @return BuyerEmail
     */
    public function buyerEmail(): BuyerEmail
    {
        return $this->buyerEmail;
    }

    /**
     * @return BuyerFullName
     */
    public function buyerFullName(): BuyerFullName
    {
        return $this->buyerFullName;
    }
}

```

## Domain Events

Of course, `Domain Events` are the essential part in `Event Sourcing`.

`Domain Events` should be `Immutable` and implements `DomainEvent` and they are located in `Domain/Service/Models/Events`

- We create our first Event in past-tense `UserAsBuyerRegistered`
- We need to identify our Event name and has to begin with `Context` like in the example
- As our events are `Immutable` so they don't have any setter methods
- We start to provide a getter method for the event payload fields

```php
final class UserAsBuyerRegistered implements \Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent
{
    use ImmutableEventTrait;

    private const name = 'Boilerplate.UserAsBuyerRegistered';

    public const BUYER_ID = 'buyerId';
    public const BUYER_EMAIL = 'buyerEmail';
    public const BUYER_FULL_NAME = 'buyerFullName';

    /**
     * @var BuyerId
     */
    private $buyerId;

    /**
     * @var BuyerEmail
     */
    private $buyerEmail;

    /**
     * @var BuyerFullName
     */
    private $buyerFullName;

    /**
     * @return BuyerId
     */
    public function buyerId(): BuyerId
    {
        return $this->buyerId;
    }

    /**
     * @return BuyerEmail
     */
    public function buyerEmail(): BuyerEmail
    {
        return $this->buyerEmail;
    }

    /**
     * @return BuyerFullName
     */
    public function buyerFullName(): BuyerFullName
    {
        return $this->buyerFullName;
    }
}

```

## Value Objects

Our `Value Objects` should always be `final` and `Immutable` and they can be a simple or complex Value objects.

For example, It can be `BuyerId` or could be the `BuyerFullName` but they share common functionality

- It's `final` class
- They are `Immutable` by using `private` constructor
- They are located `Domain/Service/Models/ValueObjects`
- We have to validate that our `Value object` value is valid inside the constructor

## Repository

Our `Repository` should extend `MultiModelStoreAggregateRepository` and has to implement a couple of functions

- `Save` method which take the `Aggregate` and `Command` objects as parameters
- `get` method take `AggregateId` as a parameter
- `getStreamName` method which defines to which event stream should persist/get the events
- `getCollectionName` method which defines which collection should be used as `Aggregate State`
- `getAggregateTypeMap` method which map `Behavior Class`, `State Class` and `Aggregate Type`
- `getEventTranslatorMap` method which map `Event Name` to the `Event Class`

```php
class UserAsBuyerRepository extends MultiModelStoreAggregateRepository implements UserAsBuyerRepositoryContract
{
    /**
     * @param Buyer $buyer Buyer
     * @param Command $command Command
     * @throws Throwable
     */
    public function save(Buyer $buyer, Command $command): void
    {
        $this->saveAggregate($buyer, $command);
    }

    /**
     * @param BuyerId $buyerId BuyerId
     * @return Buyer
     */
    public function get(BuyerId $buyerId): Buyer
    {
        return $this->getAggregate(AggregateId::fromValueObject($buyerId));
    }

    /**
     * @return string
     */
    protected function getStreamName(): string
    {
        return 'buyers-stream';
    }

    /**
     * @return string
     */
    protected function getCollectionName(): string
    {
        return 'buyers';
    }

    /**
     * @return AggregateTypeMap
     */
    protected function getAggregateTypeMap(): AggregateTypeMap
    {
        return AggregateTypeMap::fromArray([
            AggregateTypeMap::AGGREGATE_BEHAVIOR_CLASS => Buyer::class, // Aggregate Class
            AggregateTypeMap::AGGREGATE_STATE_CLASS => State::class, // Aggregate State Class
            AggregateTypeMap::AGGREGATE_TYPE => 'Boilerplate.Buyer', Aggregate Name
        ]);
    }

    /**
     * @return EventTranslator
     */
    protected function getEventTranslatorMap(): EventTranslator
    {
        return new EventTranslator([
            UserAsBuyerRegistered::eventName() => UserAsBuyerRegistered::class,
        ]);
    }
}
```

## Process Managers

Our last part would be `Process Managers` or `Policies` which is basically a listener to an event that would need to trigger another command.

For example, let's say we have an event `UserAsBuyerRegistered` has been triggered and we have a listener to this event that will trigger another `Command` to send an email to the buyer.

In order to develop such a listener, please refer to [Produce & Consume Events](produce-consume-events-shared-eventstore.md) as you need to consume from your own event store and take action `Async` like send email to buyer.
