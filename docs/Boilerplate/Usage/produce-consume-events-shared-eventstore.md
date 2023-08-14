# Produce & Consume Events to/from Shared EventStore

With boilerplate v1.8.1+, Services could start to produce and consume events to and from a Shared EventStore (Postgres Database).

We will use the Shared EventStore as a way of communication between services through events.

## How to Produce

In order to produce events to the Shared EventStore, they have to be persisted on service internal Database first (It's applying produce after persist rule).

### Produce on-demand

This mechanism is based on triggering the producing projection when a new event is persisted in the service event stream.
This mechanism is **recommended** as it reduces the load on the service and the producing projection ends properly once it produces the event and frees up cpu and memory.

- Persist the events inside the service database
- We need to specify:
  - projection unique name
  - a stream name: DB table name that contains the list of events
  - background_process: set it to `true` if should keep the process always running but it's recommended to set it to `false` and it will run the projection on-demand

  In order to do that, we need to open `src/Infrastructure/Boilerplate/Laravel/config/projections.php` and adjust the producing configuration as follows

  ```php
  'producing' => [
    'boilerplate-users-projection' => [
      'stream_name' => 'boilerplate-users-stream',
      'background_process' => false,
    ],
  ],
  ```

- It's required to enable Laravel queues
  - It's required to uncomment in `.ops/docker/Dockerfile`
    - COPY .ops/docker/config/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
- To test it locally, please run artisan command `projector:producing:start boilerplate-users-projection`

### Produce with long running process

This is a mechanism where the projection is always running in the background.
It's important to **use it in rare cases** as it's not recommended since it causes cpu and memory issues.

- Persist the events inside the service database
- We need to specify:
  - projection unique name
  - a stream name: DB table name that contains the list of events

  In order to do that, we need to open `src/Infrastructure/Boilerplate/Laravel/config/projections.php` and adjust the producing configuration as follows

  ```php
  'producing' => [
    'boilerplate-users-projection' => [
      'stream_name' => 'boilerplate-users-stream',
    ],
  ],
  ```

- In `\Application\Core\Console\Kernel`, we have a scheduled Job `runProducingProjections` that run all producing projections specified in the `projections.php` file.
- To enable `CronJob`
  - It's required to uncomment `schedule:run` command in `.ops/docker/config/crontab`
  - It's required to uncomment in `.ops/docker/Dockerfile`
    - COPY .ops/docker/config/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
    - COPY .ops/docker/config/dcron.conf /etc/supervisor/conf.d/dcron.conf
    - COPY .ops/docker/config/crontab /var/spool/cron/crontabs/www-data
    - RUN apk add --update --no-cache dcron
- To test it locally, please run artisan command `projector:producing:start boilerplate-users-projection`

### Combination of Produce on-demand and Produce by long running process

It's possible to combine different producing mechanisms for different streams but it's important to enable the needed `CronJob` and `Laravel Queues` as mentioned above.mechanism

  ```php
  'producing' => [
    'boilerplate-users-projection' => [
      'stream_name' => 'boilerplate-users-stream',
    ],
    'boilerplate-projects-projection' => [
      'stream_name' => 'boilerplate-users-stream',
      'background_process' => false,
    ],
  ],
  ```

## How to Consume

### Consume from Shared EventStore

In order to consume events **from the Shared EventStore**, we need to do the following:

- We need to specify a projection unique name and a stream name (DB table name that contains the list of events).

  In order to do that, we need to open `src/Infrastructure/Boilerplate/Laravel/config/projections.php` and adjust the consuming configuration as follows

  ```php
  'consuming' => [
    'buyer-search_profile-projection' => [
      'stream_names' => [
          'buyer-search_profiles-stream',
      ],
      'environment' => Environment::SHARED,
      'handler' => BuyerSearchProfileCreatedEventHandler::class,
    ],
  ],
  ```

- As specified in `handler` key, It's also required to specify the class that will handle this event by using `EventMappers` and `EventHandler`

  ```php
  declare(strict_types=1);

  namespace Application\v1\TestService\EventMappers\SearchProfile;

  use Allmyhomes\EventProjections\Services\EventHandlers\EventMapper;

  class BuyerSearchProfileCreatedEventMapper extends EventMapper
  {
      /**
        * @return string
        */
      public function getRoomMinSize(): string
      {
          return $this->get('room_min_size');
      }

      /**
        * @return string
        */
      public function getRoomMaxSize(): string
      {
          return $this->getOrDefault('room_max_size', '0');
      }
  }
  ```

  ```php
  declare(strict_types=1);

  namespace Application\v1\TestService\EventHandlers\SearchProfile;

  use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
  use Allmyhomes\EventProjections\Contracts\EventHandlers\EventHandlerInterface;

  class BuyerSearchProfileCreatedEventHandler implements EventHandlerInterface
  {
      /**
        * @param EventDTO $event event
        * @return void
        */
      public function handle(EventDTO $event): void
      {
          $eventMapper = new BuyerSearchProfileCreatedEventMapper($event);

          // Business logic to handle the event using $eventMapper
      }
  }
  ```

- In `\Application\Core\Console\Kernel`, we have a scheduled Job `runConsumingProjections` that run all consuming projections specified in the `projections.php` file.
- To enable `CronJob`
  - It's required to uncomment `schedule:run` command in `.ops/docker/config/crontab`
  - It's required to uncomment in `.ops/docker/Dockerfile` the following lines
    - COPY .ops/docker/config/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
    - COPY .ops/docker/config/dcron.conf /etc/supervisor/conf.d/dcron.conf
    - COPY .ops/docker/config/crontab /var/spool/cron/crontabs/www-data
    - RUN apk add --update --no-cache dcron
- To test it locally, please run artisan command `projector:consuming:start buyer-search_profile-projection`

### Consume from Local EventStore

The service has the ability to consume events from its own EventStore by specifying the environment to `Local`.

It applies the whole process mentioned in [Consume From Shared EventStore](#consume-from-shared-eventstore) with different in configuration of the projection.

#### Consume on-demand

This mechanism is based on triggering the consuming projection when a new event is persisted in the service event stream.
This mechanism is **recommended** as it reduces the load on the service and the consuming projection ends properly once it consumes the event and frees up cpu and memory.

We need to configure our projection to consume from `local` event stream and on-demand as follows

  ```php
  'consuming' => [
    'buyer-search_profile-projection' => [
      'stream_names' => [
          'buyer-search_profiles-stream',
      ],
      'environment' => Environment::LOCAL,
      'handler' => BuyerSearchProfileCreatedEventHandler::class,
      'background_process' => false,
    ],
  ],
  ```

#### Consume with long running process

This is a mechanism where the projection is always running in the background.
It's important to **use it in rare cases** as it's not recommended since it causes cpu and memory issues.

We need to configure our projection to consume from `local` event stream and on-demand as follows

  ```php
  'consuming' => [
    'buyer-search_profile-projection' => [
      'stream_names' => [
          'buyer-search_profiles-stream',
      ],
      'environment' => Environment::LOCAL,
      'handler' => BuyerSearchProfileCreatedEventHandler::class,
      'background_process' => true,
    ],
  ],
  ```

## Retry failed projections on demand (consuming and producing)

Projections can fail for various reasons and if they are run on demand, you need to wait for an event that triggers
the start of the projection and picks up the failed event, after you deployed your fix. Sometimes, events occur
not so often and you want to trigger the projection manually.

For that, we implemented an artisan command which can be run after a successful deployment for example via jenkins.

You just need to run `php artisan projections:retry` and all failed jobs for producing and consuming on demand will be retried.

To ensure that just the jobs for producing and consuming on demand will be retried, we changed the queue where these jobs will be queued.
Jobs for producing and consuming on demand will be in the queue `domain-events` and all other laravel jobs/events will be inside the default queue.

## Local Environment

- In order to test the producing or consuming logic, It's important to have the Shared EventStore installed on local machine by following
  > - Update your `cluster` setup from `master` branch
  > - Install new chart in your cluster by running `bin/install charts/event-store/`
  > - Use any Postgres GUI, for example OmniDB or any other GUI you prefer
  > - Connect to your local event-store with server `api.dev.local`, port `31578` and username/password same as always and username/password same as always and should have 2 databases (event-store, event-store_test)
  >
  > **Hint**: If `event-store_test` wasn't created, please run this command
    `docker run postgres:11-alpine sh -c "export PGPASSWORD='password'; psql -h '$(minikube ip)' -p 31578 -d 'event-store' -U 'root' -c 'CREATE DATABASE \"event-store_test\";'"`

- In case you do code changes, it's required to **stop** your producing/consuming artisanjob and start it again.

- If would like to test the cron process locally
  - need to run the following command every time goes inside your service:

  ```shell
  bin/shell <service>
  mkdir -p /etc/supervisor/conf.d/
  mkdir -p /var/spool/cron/crontabs/
  cp .ops/docker/config/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
  cp .ops/docker/config/dcron.conf /etc/supervisor/conf.d/dcron.conf
  cp .ops/docker/config/crontab /var/spool/cron/crontabs/www-data
  apk add --update --no-cache dcron

  crond -f -S
  ```

  - open another shell inside your terminal and start to use it and watch your log file `src/Infrastructure/Boilerplate/Laravel/storage/logs/laravel.scheduler.log`

## Remote Environment

- It's quite important to enable `CronJobs` on your service as mentioned
  - To enable `CronJob`
    - It's required to uncomment `schedule:run` command in `.ops/docker/config/crontab`
    - It's required to uncomment in `.ops/docker/Dockerfile`
      - COPY .ops/docker/config/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
      - COPY .ops/docker/config/dcron.conf /etc/supervisor/conf.d/dcron.conf
      - COPY .ops/docker/config/crontab /var/spool/cron/crontabs/www-data
      - RUN apk add --update --no-cache dcron
- In case you do code changes in your `EventHandlers`, it's required to **restart** your consuming job so it detects the code changes

## Updates

- Starting from `v1.2.0` of `allmyhomes/php-event-projections`, it's possible to **consume** from multiple streams by running
  a single projection by passing list of streams in `stream_names` but this option **can** only
  run on new projections not on previous consuming projections. The main idea is to reduce the memory and cpu load as well as
  consuming from 2 or more different streams by single event handler feature is needed.

- For consuming part, could use `stream_names` key in `src/Infrastructure/Boilerplate/Laravel/config/projections.php` instead of `stream_name` but still `stream_name` is supported and will be removed only in a major update.
