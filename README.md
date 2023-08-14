# Domain Driven Laravel API boilerplate

[![pipeline status](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/badges/master/pipeline.svg)](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/commits/master)
[![coverage report](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/badges/master/coverage.svg)](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/commits/master)

![Laravel Backend Boilerplate](https://laravel.com/assets/img/components/logo-laravel.svg "Laravel Backend Boilerplate")

## Table of Contents

- [Description](#description)
- [Motivation](#motivation)
- [Tech & Core Packages](#tech--core-packages)
- [Features](#features)
- [Migration Process](#migration-process)
- [Installation](#installation)
- [How to use](#how-to-use)
- [Contributing](#contributing)
- [Profiling the Application (API and CLI)](docs/Boilerplate/Usage/blackfire.md)
- [Responsible Team](#responsible-team)
- [References](#references)

----

### Description

Laravel 6.x boilerplate optimized for building a microservice API following the [Domain Driven Design Pattern](https://medium.com/@munza/large-scale-laravel-application-9d52c3d38e51).

### Motivation

The boilerplate stands as a source of structure and base packages that Allmyhomes services use.

Each new service starts by getting the latest boilerplate as it's core structure, so it helps to guarantee
that our services looks similar and easier to understand for all the technical department.

Also the boilerplate is always with changes and new features that could be easily adapted by the Product Teams.

In general, the boilerplate is meant to enforce the following principles:

- *Clean separation of presentation, domain/logic and data/persistence layers*
- *Resource abstraction* (Repository Pattern)
- *Service Layer abstraction* (Service Layer Pattern)

### Tech & Core Packages

The _development_ or _remote_ environment is expected to provide the following dependencies:

- PHP >= 8.0
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension

The boilerplate is based on

- Laravel 6
- Postgres 11.x

Also, it ships with the following core packages:

- [Allmyhomes laravel ddd abstractions](https://gitlab.smartexpose.com/allmyhomes/laravel-ddd-abstractions)
- [Allmyhomes laravel token verification](https://gitlab.smartexpose.com/allmyhomes/laravel-token-verification)
- [Allmyhomes laravel Keycloak Guard](https://gitlab.smartexpose.com/allmyhomes/laravel-keycloak-guard-package)
- [Allmyhomes php event projections](https://gitlab.smartexpose.com/allmyhomes/php-event-projections)
- [Allmyhomes contract testing](https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/CONTRACT-TESTING-CONTRACT-VALIDATION.md)
- [Event Engine](https://event-engine.io)
- [laravel-cors](http://github.com/barryvdh/laravel-cors)
- [kamermans/guzzle-oauth2-subscriber](https://github.com/kamermans/guzzle-oauth2-subscriber)
- [friendsofphp php-cs-fixer](https://cs.symfony.com/)
- [PHPStan](https://github.com/phpstan/phpstan)

----

### Features

#### Event Sourcing based

The boilerplate provides a mechanism as a process for Event Driven Architecture.

- [Complete ES/CQRS](docs/Boilerplate/Usage/event-sourcing-boilerplate-how-to-use.md)

#### Producing and Consuming Events

The boilerplate provides the infrastructure needed to produce and consume events to our shared EventStore.
A usage example could be [here](docs/Boilerplate/Usage/produce-consume-events-shared-eventstore.md)

#### Contract Testing

It's essential to have a consistency between our OpenApi2 contract and our implementation. We build a tool that validate
service contracts with service implementation. For full details please check [Contract Testing](https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/CONTRACT-TESTING-CONTRACT-VALIDATION.md)

#### Continuous Integration

The GitLab CI pipeline is configured in `.gitlab-ci.yml` by [DevOps](https://gitlab.smartexpose.com/allmyhomes/devops)
The pipeline normally runs multiple stages such as `test` and `deploy`.

To ensure that the latest status of the pipeline is visualized on the GitLab client,
add the lines below to the top of this README.md file and replace `YOUR_SERVICE` in
the `pipeline status` and `coverage report`, as follows:

- [![pipeline status] + (https://gitlab.smartexpose.com/allmyhomes/YOUR_SERVICE/badges/master/pipeline.svg)](https://gitlab.smartexpose.com/allmyhomes/YOUR_SERVICE/commits/master)
- [![coverage report] + (https://gitlab.smartexpose.com/allmyhomes/YOUR_SERVICE/badges/master/coverage.svg)](https://gitlab.smartexpose.com/allmyhomes/YOUR_SERVICE/commits/master)

----

### Migration Process

The boilerplate mainly maintained and updated with infrastructure features, so there will be always changes
that's for services will need to migrate the latest boilerplate version.

The boilerplate applies [Semantic Versioning 2.0.0](https://semver.org/).

Therefore, we have our standards for services to migrate to boilerplate as well as the period of maintenance for old versions by SRE Team.

- PATCH Update -> Service has to upgrade in same sprint as fast as possible (bug fix).
- MINOR Update -> Service has to upgrade in next sprint, therefore any bugs of old version will be maintained by SRE team.
- MAJOR Update -> Service has to upgrade within next 3 sprints, therefore any bugs/features of old versions will be maintained by SRE team.

Service could follow this [migration guideline](https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/backend/boilerplate/MICROSERVICES-MERGING-BOILERPLATE.md) as step by step guideline.

----

### Installation

#### Minikube

- Clone the `laravel-api-boilerplate` GitLab repository:

```sh
git clone https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate [service_name-service]
```

- To install dependencies and run a new service, follow the instructions in the Kubernetes (K8s) [cluster-setup](https://gitlab.smartexpose.com/allmyhomes/devops/cluster-setup/blob/master/README.md):

For development of the `boilerplate` itself:

```sh
# add the Helm chart if necessary
$ bin/install charts/be-laravel-boilerplate

# switch to local mounted directory
$ bin/switch2devel charts/be-laravel-boilerplate {PATH_TO_SOURCE_CODE}/boilerplate

# Enter the running container in K8s
$ bin/shell boilerplate
```

**Reminder:**
Every time you need to invoke an _artisan_ Command or _composer_ script, you must enter the running K8s container.

- Install _composer_ dependencies:

```sh
composer install
```

- To re-generate an **APP_KEY** which is automatically written to .env:

```sh
php artisan key:generate
```

- Test boilerplate development environment:

```sh
# create User DB Table (using database/migrations/*_create_users_table.php)
$ php artisan migrate

# run phpcs (code sniffer)
$ composer cs-check

# run tests
$ composer test
```

- Setup the authentication, please follow [Authentication guideline](https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/AUTHENTICATION.md)

#### Docker Compose

It's possible to run a boilerplate container using `docker-compose` in easy steps:

- Clone the `laravel-api-boilerplate` GitLab repository:

    ```shell script
    git clone https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate [service_name-service]
    ```

- Pull our latest Allmyhomes base image where `Target` is `base-testing` and `PHP_VERSION` could be `major.minor` as provided by DevOps

  ```shell script
  docker pull eu.gcr.io/amh-infrastructure/laravel/${TARGET}:${PHP_VERSION}
  ```

- Run docker build

  ```shell script
  docker-compose build
  ```

- Run docker up

  ```shell script
  docker-compose up
  ```

----

### How to use

- [Produce & Consume Events to Shared EventStore](docs/Boilerplate/Usage/produce-consume-events-shared-eventstore.md)
- [How to use Event Sourcing in Boilerplate v2.0.0](docs/Boilerplate/Usage/event-sourcing-boilerplate-how-to-use.md)
- [How to migrate existing service to use Event Sourcing](docs/Boilerplate/Migrations/service-migration-to-eventsourcing.md)
- [Sync External Microservice Communication](docs/Boilerplate/Usage/external-microservice-communication-rest-api.md)
- [PHPStan](docs/Boilerplate/Usage/phpstan.md)

----

### Mail Renderer Client

This service uses the [Mail Renderer Client](https://gitlab.smartexpose.com/allmyhomes/site-reliability-engineering-team/mail-renderer-client)  in order to send emails via the [Mail Renderer Client](https://gitlab.smartexpose.com/allmyhomes/site-reliability-engineering-team/mail-renderer-service).

Read more about the client and service & how it is set up [here](https://allmyhomes.atlassian.net/wiki/spaces/AC/pages/194249773/Distributed+Mail+System).
#### Lokal Testing

Its not possible so far to test and check how the email templates look like.
However, you can check if an email is successfully sent to the receiver.

If you want to locally check if emails are getting send with the mail renderer client you need to adjust the `tilt-config.yaml` to use the `auth` & `mail-renderer` chart.

Make sure you pull the latest version of the microservice-infratsructure repository and put the following `OAuth credentials` in the `values.yaml` in the local namespace:
```
# credentials of Context Mail Boilerplate
APP_AMH_OAUTH_CLIENT_ID: 38 
APP_AMH_OAUTH_CLIENT_SECRET: 3WY1nQU6yYcgpn5EFwGQ96v03H9kJgksrZjqafGXC
```

Then trigger the email sending use case locally & check a respective log or event that has been created in the database.

#### Testing email templates in deployed environments

To check if the sent email templates are correct, you need to trigger the use case for sending the email on the desired environment (besides live) & checkout the mailhog instance (e.g. mailhog.develop.envs.ioof the respective environment.




### Contributing

Your contribution is very valuable. Please check our [contributing guideline](CONTRIBUTING.md)

----

### Responsible Team

- [SRE Team](https://gitlab.smartexpose.com/allmyhomes/site-reliability-engineering-team)

----

### References

- Architecture and Design Patterns
  - [Large scale Laravel Application](https://medium.com/@munza/large-scale-laravel-application-9d52c3d38e51)
    - [Transformers](https://medium.com/@haydar_ai/how-to-start-using-transformers-in-laravel-4ff0158b325f)
    - [Repository Pattern](https://medium.com/@sinsin_78919/the-repositories-pattern-in-lumen-75cf08145d96)
    - [Command Pattern](https://tactician.thephpleague.com)
  - [Lucid Architecture](https://github.com/lucid-architecture/laravel-microservice)
  - [PHP Design Patterns](http://designpatternsphp.readthedocs.io/en/latest/README.html)
  - [Observers](https://medium.com/@secmuhammed/create-command-for-laravel-observers-3a0a65582aa4)

- SOLID
  - [Single-action class](https://medium.com/@remi_collin/keeping-your-laravel-applications-dry-with-single-action-classes-6a950ec54d1d)
  - [Layers](https://medium.com/@ivelinpavlov/the-layers-attack-ca8750202b2e)

- Tutorials
  - [blueprint-dreed](https://hackernoon.com/writing-and-testing-api-specifications-with-api-blueprint-dreed-and-apiary-df138accce5a)
  - [api-documentation-tools](https://pronovix.com/blog/free-and-open-source-api-documentation-tools)

- Credentials
  - [encrypted .env](https://medium.com/@marcelpociot/a-env-replacement-for-storing-secret-credentials-in-your-laravel-application-fdbae6c9f41b)

- Links
  - [App Layers in Laravel](https://medium.com/@ivelinpavlov/the-layers-attack-ca8750202b2e)
  - [Separation of Concerns in Laravel](https://medium.com/@jon.lemaitre/separation-of-concerns-with-laravel-s-eloquent-part-3-collections-relations-eager-loading-and-e13530a8890a)
  - [DDD in Laravel](http://lorisleiva.com/conciliating-laravel-and-ddd)

- Patterns
  - [Design Patterns in PHP](https://github.com/domnikl/DesignPatternsPHP )
  - [Repository Pattern](https://medium.com/@sinsin_78919/the-repositories-pattern-in-lumen-75cf08145d96)
  - [Service Pattern](https://m.dotdev.co/design-pattern-service-layer-with-laravel-5-740ff0a7b65f)
  - [Container Pattern](http://container.thephpleague.com)
  - [Command Pattern](https://tactician.thephpleague.com)

- Versioning
  - [Microservice Versioning](https://blog.travelex.io/microservice-versioning-a75d34d575)
