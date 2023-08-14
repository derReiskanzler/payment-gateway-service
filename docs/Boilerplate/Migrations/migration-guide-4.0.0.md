# v4.0.0 Migration Guide

This document outlines the changes applied in Boilerplate **v4.0.0** from **v3.10.0**.

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Changes](#changes)
  - [High Impact Changes](#high-impact-changes)
    - [Hexagonal Architecture](#hexagonal-architecture)
    - [Directory structure](#directory-structure)
    - [Drop support for MySQL](#drop-support-for-mysql)
    - [Snake_Case for Aggregate State ReadModel](#snake_case-for-aggregate-state-readmodel)
    - [Exception Handler & Rendering](#exception-handler--rendering)
    - [API routes](#api-routes)
    - [Import statements](#import-statements)
    - [Remove of Codeception](#remove-of-codeception)
  - [Medium Impact Changes](#medium-impact-changes)
    - [Boilerplate Code](#boilerplate-code)
    - [Module Generation](#module-generation)  
  - [Low Impact Changes](#low-impact-changes)
    - [Interface instead of Contract](#interface-instead-of-contract)
    - [Codesniffer](#codesniffer)

## Upgrade Guide

To upgrade to v4.0.0, please follow this [guide](./boilerplate-migration.md)

Since now boilerplate and framework code are placed under `src/Infrastructure/Boilerplate` you need to move all files
you created in the directories `Application/Core` and `Infrastructure/` to their new location.

## Changes

### High Impact Changes

#### Hexagonal Architecture

The hexagonal architecture, or ports and adapters architecture, is an architectural pattern used in software design. It aims at creating loosely coupled application components that can be easily connected to their software environment by means of ports and adapters. This makes components exchangeable at any level and facilitates test automation - [wikipedia][1]

Core Benefits:

- At least part of a code (business core) can be understandable by non-programmers (business analysts, product owners, your parents, etc.)
- The core code is decoupled from the infrastructure which makes very easy to replace the adapters without changing the business core code
- The core is agnostic of an application framework, the old one can be replaced to whatever other framework is popular at the moment
- Writing unit tests for the core is very simple and fast, we don’t need to create framework-specific test set up, simple PHP will be enough.

Nice articles and important to read:

- <https://wkrzywiec.medium.com/ports-adapters-architecture-on-example-19cab9e93be7>
- <https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/>
- <https://matthiasnoback.nl/2017/08/layers-ports-and-adapters-part-2-layers/>

#### Directory structure

Please follow new rules of code placement, organization and seggregation between the layers.
* The directories `Application`, `Domain` and `Infrastructure` are being moved to `src`. Please make sure you move all your 
files accordingly and following this [structure](../Usage/src-structure.md)

The `Application` directory contains the following types of classes
- For Handling Aggregates
  - Command
  - CommandHandler (formerly Service classes containing no business logic)
  - ProcessManager (for consuming events)
- For Handling Read Models
  - Projector (for consuming events)
  - Repository (Interfaces)
  - Document (the read model)
- For Publishing Events
  - Publisher
  - PublicEvent

The `Domain` directory contains the following types of classes
- For Handling Aggregates
  - Aggregate
  - AggregateState
  - DomainEvent
  - Exception
  - Repository (Interfaces)
  - ValueObject (Aggregate specific ones)
- In general
  - ValueObject (shared ones)

The `Infrastructure` directory contains the following types of classes
- Controller, Request
- Repositories (implementation)
- ApiClient (external calls)

If not already done you need to adjust your service to this structure.

#### Drop support for MySQL

Starting from v4+, the boilerplate only supports **postgres**.

#### Snake_Case for Aggregate State ReadModel

The `MultiModelStoreAggregateRepository` (base class for our repositories) now enforces `snakes_case` keys in the database.

However, to make this work, we need to use the trait `ImmutableRecordLogic` in our `AggregateState` classes and make sure
to always write the values of the `const` variables in `camelCase`.

**Note:**

- Write operations are handled by default from the Infrastructure
- Service might need to check the `Query` side as it might need to either rebuild the `Aggregate State` ReadModel and update possible Query transformations.
- Please make sure to cleanly remove your self written
  logic in case you've created something which provides a similar behavior.

#### Exception Handler & Rendering

We removed the old Exception Handler since it was deprecated in v3.x. Please remove the following statement from the middleware of your
route files: `handle.exception`

To recap on changes in new Allmyhomes Exception Handler, please refer to [concept][2].

#### API routes

We started to have 2 API routes to have a clear seggregation between Boilerplate API endpoints and service endpoints. 
- `src/Infrastructure/Boilerplate/Laravel/routes/api.php` => It's related to Boilerplate routes like healthz endpoint.
- `src/Infrastructure/Inbound/Api/Route/api.php` => This is where we should declare the service API endpoint.

#### Import statements

As a new rule for import statement resulting from code created by allmyhomes developers we updated the `composer.json`
file to link everything under `src/` to `Allmyhomes`. 

This means you have to update all import statements of your self
written to `use Allmyhomes/Application/...`, `use Allmyhomes/Domain/...` or `use Allmyhomes/Infrastructure/...`
accordingly.

#### Remove of Codeception

Since Codeception is no longer supported you are required to migrate all of your tests to PHPUnit if not already done.

#### PHPStan

PHPStan has been set to level `8` and upgraded to first stable version `1.x` which requires some additional changes. 

For example:
- array declarations in method names now require a special comment to ensure that they are validated correctly
  ```php
    /**
     * @param array<string, string|int> $eventPayload
     */
    public function testHandleLogic(array $eventPayload): void
  ```
- for `nullable` objects it is now required to either use the null safe operator `?->` or make sure it is not `null`

### Medium Impact Changes

#### Boilerplate Code

The whole boilerplate code changed:

- PHP8 syntax
- Stricter codesniffer ruleset only for the boilerplate code till now
- Public assets and extra files related to views and Laravel as MVC framework are removed
- All boilerplate tests are migrated to PHPUnit.

So please don't be surprised if could see many changes ;)

#### Module Generation

The complete module generation and stubs are removed as they aren't compatible with our EDA.

### Low Impact Changes

#### Interface instead of Contract

Since we decided to use the naming `Interface` instead of `Contract`.

Please rename your files accordingly:
e.g. `RepositoryContract` to `RepositoryInterface` or `ServiceContract` to `ServiceInterface`

#### Codesniffer

We migrated our codesniffer to use `symfony cs-fixer` since it's more maintainble and up to date with latest php versions.

The migration to the new codesniffer requires you to run `composer cs-check` again and fix all errors which pop up
(the new codesniffer is stricter than the old one) but also much easier to fix automatically so please run `composer cs-fix`.

[1]: <https://en.wikipedia.org/wiki/Hexagonal_architecture_(software)>
[2]: <https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/RESTFUL-API-DESIGN.md#errors-and-exceptions-structure>
