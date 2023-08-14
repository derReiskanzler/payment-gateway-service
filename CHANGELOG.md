<!--- BEGIN HEADER -->
# Changelog

All notable changes to this project will be documented in this file.
<!--- END HEADER -->

## [4.3.0](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.2.1...4.3.0) (2022-05-09)
### Features

* Add default migration classes [[f35a28](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/f35a28953f19cc90ece11e671f40dc4574ce7888)]

### Bug Fixes

* Bind document store interface to implementation [[9c9388](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/9c9388186df323531c9c3f2a86edcb2e98c5f44c)]

---

## [4.2.1](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.2.0...4.2.1) (2022-05-03)

### Bug Fixes

* Set AMH_BOILERPLATE_VERSION=4.2.1 in .env.example [[2734a5](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/2734a5c4e9ffe7e3a4bfa48e3c2b151f835eac1d)]

---

## [4.2.0](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.1.0...4.2.0) (2022-04-21)
### Features

* Add mail-rendering config to host assets [[d73c9b](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/d73c9b6e1d136bd9909dacc8615ee63f5acf5de3)]
* Configure emergency logger to log to stdout [[ccdfac](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/ccdfac7e1475bc756086405e86c90e4377f52eb9)]
* Update composer packages
* Update `event-engine/php-postgres-document-store` to v0.14
* Update `prooph/pdo-event-store` to v1.14

### Deprecated

* Interface `src/Infrastructure/Boilerplate/Helpers/Support/Interfaces/Arrayable.php` is deprecated and will be removed

### Bug Fixes

* Bind UuidFactoryInterface to implementation [[b1ab72](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/b1ab72564244897b99e0693a4d3b6d5dbcdcac5c)]
* Start script doesnt change namespaces in composer.json [[a17764](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/a177647cfe5f62f858d30f812c72c2663823a991)]

---

## [4.1.0](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.4...4.1.0) (2022-02-09)
### Features

* Implement VerifyJWK-Middleware ([SRE-2181](https://allmyhomes.atlassian.net/browse/SRE-2181)) [[63e2c1](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/63e2c1ffc8d315095be5be3584aa54a43932aefe)]
* Upgrade laravel pipeline to `v1.7.0` to enable test coverage visualization and disable api contract version pipeline job [[13ab20](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/13ab20766181044d45b557b56df0bfb796fa0737)]

### Bug Fixes

* Adjust resources/views path to match with the new folder structure [[d4fd5c](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/d4fd5c4a473e00730fba483d645923878bbdff05)]

---

## [4.0.4](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.3...4.0.4) (2022-01-27)
### Bug Fixes

* TraceIdLoggingProcessor expects Illuminate Logger [[55594d](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/55594d0f0cfe3e9a6961585408f510aa6dd49b35)]

---

## [4.0.3](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.2...4.0.3) (2022-01-12)
### Bug Fixes

* Autoload of Model trait in allmyhomes/laravel-uuid [[123c2d](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/123c2d06502da007e734ca46eab997bcea83020e)]

---

## [4.0.2](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.1...4.0.2) (2022-01-04)
### Bug Fixes


##### Oauth2httpclient

* GetAuthCredential method declaration with proper return types ([SRE-2076](https://allmyhomes.atlassian.net/browse/SRE-2076)) [[b054ad](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/b054adbb663c129f73133901a2c16b3d20ccaf5d)]

---

## [4.0.1](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.0...4.0.1) (2021-12-13)
### Bug Fixes

* Update allmyhomes/contract-utils package [[01b28a](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/01b28a919ba0916f9f488565fbedfe42d734695c)]

---

## [4.0.0](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/3.10.0...4.0.0) (2021-12-10)
### ⚠ BREAKING CHANGES

##### PHP

* Use Latest PHP 8.0.13 in `.ops/docker/Dockerfile`

##### MySQL

* Completely drop support of MySQL

##### Boilerplate

* Added [migration guideline](docs/Boilerplate/Migrations/boilerplate-migration.md)
* Remove Module Generation
* Remove deprecated `CustomJsonSerializer`
* Remove copied Enum package
* Remove `CallActionTrait`
* Remove `Tree` & `Nodes` helpers
* Remove `NestedModulesHelper`
* Remove `FileIOHelper`, `UrlHelper` and `ArrayHelper`

##### Laravel

* Move laravel files to Infrastructure [[951d20](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/951d20cf8202bd13d92d3db6a8e79b7fed4e504e)]
* Clean up from JS, CSS, views files
* Clean up `public` folder
* Clean up `scripts` folder

##### Code Quality

* Increase `phpstan` to level `8` and upgrade to first stable version `1.x`
* set `checkMissingIterableValueType` to `true`
* Use `cs-fixer` as our maintainable and up to date ruleset
* Remove `allmyhomes codesniffer` package starting from v4.x of Boilerplate

##### Event-Driven 

* Multimodelstore: Force `snake_case` for document payload [[5242f9](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/5242f9ca5fddd76d8a8dd274dad3eae54ee085fa)]
* CQRS: Add `CommandIdGenerator` [[631479](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/commit/6314791fa33316880f5c405cb82153e2a5fdc726)]
* CQRS: Deprecate `generate` in `CommandId` [[631479](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/commit/6314791fa33316880f5c405cb82153e2a5fdc726)]
* CQRS: Deprecate `AbstractCommand` class [[631479](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/commit/6314791fa33316880f5c405cb82153e2a5fdc726)]

##### Src Structure

* A complete change of architecture & code should be inside `src/` - Please check [migration guideline](./docs/Boilerplate/Migrations/migration-guide-4.0.0.md)

##### Exception Handling

* Remove deprecated exception handling in boilerplate v3.1.0
* Remove `ExceptionHandlerMiddleware` as it was used to BC from old deprecated exception handler to the new handler

##### Test

* Migrated all codeception tests to phpunit
* Remove codeception and its related configuration

##### Backstage

* Add backstage catalog-info.yaml [[7cbf72](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/7cbf7231e9fb10b87887ece0a9870afe2da57969)

---

## [4.0.0-rc.5](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.0-rc.4...v4.0.0-rc.5) (2021-11-22)
### Features


##### Multimodelstore

* Force `snake_case` for document payload [[5242f9](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/5242f9ca5fddd76d8a8dd274dad3eae54ee085fa)]

##### CQRS

* Add `CommandIdGenerator` [[631479](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/commit/6314791fa33316880f5c405cb82153e2a5fdc726)]
* Deprecate `generate` in `CommandId` [[631479](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/commit/6314791fa33316880f5c405cb82153e2a5fdc726)]
* Deprecate `AbstractCommand` class [[631479](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/commit/6314791fa33316880f5c405cb82153e2a5fdc726)]

##### Backstage

* Add backstage catalog-info.yaml [[7cbf72](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/7cbf7231e9fb10b87887ece0a9870afe2da57969)]

##### PHP 8

* use Latest PHP 8.0.13

##### Structure

* Moved `Stream.php` from `Application` to `Infrastructure`
* Include `Route` folder in `Infrastructure/Inboud/Api` where services should include its own routes
* Renamed folder for Persistence to `Repository/Query` and `Repository/Persistence`
* Removed `examples` folder in `Application` and `Domain` folders

---

## [4.0.0-rc.4](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.0-rc.3...4.0.0-rc.4) (2021-10-11)
### Features


##### Keycloak

* Add setup to test protected endpoint with keycloak ([SRE-1672](https://allmyhomes.atlassian.net/browse/SRE-1672)) [[35d1c5](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/35d1c5d9bd35a69ea9484326463aada2a11514b5)]

##### Build

* Use laravel pipelines 1.5.6

### Bug Fixes


##### Configuration

* Increase prooph sleep time to 300ms in order to reduce number of DB transactions by factor of 3 ([SRE-1769](https://allmyhomes.atlassian.net/browse/SRE-1769)) [[0623db](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/0623db27923bf603b17ab9fd84d34e5cf7bbfb44)]

---

## [4.0.0-rc.3](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.0-rc.2...4.0.0-rc.3) (2021-09-30)
### Bug Fixes


##### Phpstan

* Added missing annotations ([SRE-1743](https://allmyhomes.atlassian.net/browse/SRE-1743)) [[3bc413](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/3bc413ac225f1fd4bc3e17fe668bbadf2cc23c68), [4f172b](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/4f172bfbd45ad4e5622e5718da3449f80047061b)]

---

## [4.0.0-rc.2](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/4.0.0-rc.1...4.0.0-rc.2) (2021-09-27)

### Bug Fixes


##### Build

* Use laravel pipelines 1.5.5 to deploy to both clusters [[93dc83](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/93dc8318c4b5f4290da2e9ecdd6b0784b23ca799)]

---

## [4.0.0-rc.1](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/3.9.0...4.0.0-rc.1) (2021-09-24)
### ⚠ BREAKING CHANGES


##### Laravel

* Move laravel files to Infrastructure [[951d20](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/951d20cf8202bd13d92d3db6a8e79b7fed4e504e)]
* Added [migration guideline](docs/Boilerplate/Migrations/boilerplate-migration.md)
* Clean up from JS, CSS, views files
* Clean up `public` folder
* Clean up `scripts` folder

##### Boilerplate Utils

* Remove deprecated `CustomJsonSerializer`
* Remove copied Enum package
* Remove `CallActionTrait`
* Remove `Tree` & `Nodes` helpers
* Remove `NestedModulesHelper`
* Remove `FileIOHelper`, `UrlHelper` and `ArrayHelper`

##### Code Quality

* Increase `phpstan` to level `max`
* set `checkMissingIterableValueType` to `true`
* Use `cs-fixer` as our maintainable and up to date ruleset
* Remove `allmyhomes codesniffer` package starting from v4.x of Boilerplate

##### PHP

* Use PHP 8 in `.ops/docker/Dockerfile`

##### MySQL & CRUD Module Generation

* Completely drop support of MySQL
* Remove Module Generation

##### Exception Handling

* Remove deprecated exception handling in boilerplate v3.1.0
* Remove `ExceptionHandlerMiddleware` as it was used to BC from old deprecated exception handler to the new handler 

##### Test

* Migrated all codeception tests to phpunit
* Remove codeception and its related configuration

---

## [3.10.0](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/3.9.2...3.10.0) (2021-11-22)

### Features


##### Backstage

* Add backstage catalog-info.yaml [[7cbf72](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/7cbf7231e9fb10b87887ece0a9870afe2da57969)]

##### PHP 8

* use PHP 8.0 instead of 7.4

##### Laravel

* replaced `emadadly/laravel-uuid` with `allmyhomes/laravel-uuid`
* Removed `beyondcode/laravel-dump-server`

---

## [3.9.2](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/3.9.1...3.9.2) (2021-10-11)

### Features


##### Build

* Use laravel pipelines 1.5.6

### Bug Fixes


##### Configuration

* Increase prooph sleep time to 300ms in order to reduce number of DB transactions by factor of 3 ([SRE-1769](https://allmyhomes.atlassian.net/browse/SRE-1769)) [[0623db](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/0623db27923bf603b17ab9fd84d34e5cf7bbfb44)]

---

## [3.9.1](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/3.9.0...3.9.1) (2021-09-27)

### Bug Fixes


##### Build

* Use laravel pipelines 1.5.5 to deploy to both clusters [[93dc83](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/93dc8318c4b5f4290da2e9ecdd6b0784b23ca799)]

---

## [3.9.0](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/compare/v3.8.1...v3.9.0) (2021-09-24)
### Features


##### Release automation

* Integrated automated generation of changelogs ([SRE-1499](https://allmyhomes.atlassian.net/browse/SRE-1499)) [[c48ce2](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/c48ce2b135f45ca8f8ad91ed44044e913282ce88)]

### Bug Fixes


##### Sentry

* Decrease SENTRY_TRACES_SAMPLE_RATE to 0.25 [[2384ac](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/commit/2384ac10010cc515131236a431e848c853f554cc)]

##### Tests

* Fix Flaky test in CatchRouteNotAvailableApi

##### Build

* Fix DinD MTU networking problem
* Fix buildkit problem with build image and invalid layers in final image and replace with kaniko
* Fix calling to NewRelic as it's removed 3rd party tool

---

## [v3.8.1] - 2021-08-25

**Changed:**

- update `laravel` pipeline configuration to `v1.2.1` - ([SRE-1631](https://allmyhomes.atlassian.net/browse/SRE-1631))

## [v3.8.0] - 2021-08-19

**Added:**

- Add Support for Sentry with custom tag of the boilerplate - ([SRE-1139](https://allmyhomes.atlassian.net/browse/SRE-1139))
- Add [guideline](docs/Boilerplate/Usage/sentry-adoption.md) to adopt sentry - ([SRE-1139](https://allmyhomes.atlassian.net/browse/SRE-1139))

**Changed:**

- update gitlab pipeline configuration to `v1.2.0` - ([OPS-660](https://allmyhomes.atlassian.net/browse/OPS-660))

**Removed:**

- Remove `Blackfire` from remote stage in `.ops/docker/Dockerfile`

## [v3.7.2] - 2021-07-29

**Changed:**

- Clean up unnecessary phpdoc and extra lines
- Backport `SnakeCaseEventPayloadTranslator` to support nested array - ([SRE-530](https://allmyhomes.atlassian.net/browse/SRE-530))

**Removed:**

- Removed `BUYER_SEARCH_BASE_URL`, `FUNNEL_MANAGEMENT_BASE_URL`, `TASK_MANAGEMENT_BASE_URL` from configs

**Fixed:**

- Use `base-testing` image when using `docker-compose` - ([SRE-1467](https://allmyhomes.atlassian.net/browse/SRE-1467))
- Add Readme section to gurantee working Boilerplate using `docker-compose` specially after removing `auth.json` - ([SRE-1467](https://allmyhomes.atlassian.net/browse/SRE-1467))
- Add `ext-json` to `composer.json`

## [v3.7.1] - 2021-07-16

**Changed:**

- moved shared pipelines project path to `allmyhomes/devops/shared-pipelines/laravel` - ([OPS-1281](https://allmyhomes.atlassian.net/browse/OPS-1281))
- Improve readability of `.gitignore` for storage keys - ([SRE-1438](https://allmyhomes.atlassian.net/browse/SRE-1438))

**Fixed:**

- Fix boilerplate version in `.env.example` and `CHANGELOG` - ([SRE-1409](https://allmyhomes.atlassian.net/browse/SRE-1409))
- Added a missing space in `Infrastructure\EventSourcing\Exceptions\AggregateNotFound` exception message - ([SRE-1345](https://allmyhomes.atlassian.net/browse/SRE-1345))
- Fix wrong return type in `state()` and `state` property to return `AggregateState` or `null` - ([SRE-1346](https://allmyhomes.atlassian.net/browse/SRE-1346))

## [v3.7.0] - 2021-06-30

**Added:**

- Add [Laravel Keycloak Guard Package](https://gitlab.smartexpose.com/allmyhomes/laravel-keycloak-guard-package/) - ([SRE-1305](https://allmyhomes.atlassian.net/browse/SRE-1305))

## [v3.6.0] - 2021-03-24

**Added:**

- Allow spaces in `APP_NAME` .env files - ([SRE-992](https://allmyhomes.atlassian.net/browse/SRE-992))
- Add command `projections:retry` to retry failed projection jobs - ([SRE-1026](https://allmyhomes.atlassian.net/browse/SRE-1026))
- Add method `getAggregateUntilVersion` to `MultiModelStoreAggregateRepository` - ([SRE-825](https://allmyhomes.atlassian.net/browse/SRE-825))

**Changed:**

- update gitlab pipeline configuration to `v1.1.3` ([OPS-660](https://allmyhomes.atlassian.net/browse/OPS-660))

**Removed:**

- removed `Qualifier Management` from configs ([SRE-614](https://allmyhomes.atlassian.net/browse/SRE-614))

**Fixed:**

- Adjust docker-compose ports for application database (port was already in use by event-store) - ([SRE-991](https://allmyhomes.atlassian.net/browse/SRE-SRE-991))

## [v3.5.1] - 2021-02-24

**Fixed:**

- fix http response code if exception code is below 100 or above 599 ([SRE-989](https://allmyhomes.atlassian.net/browse/SRE-989))

## [v3.5.0] - 2020-12-07

**Added:**

- add consuming of local events on-demand ([SRE-750](https://allmyhomes.atlassian.net/browse/SRE-750))
- add producing of local events on-demand ([SRE-749](https://allmyhomes.atlassian.net/browse/SRE-749))
- add route caching in dockerfile ([SRE-752](https://allmyhomes.atlassian.net/browse/SRE-752))
- add ability to enable config caching in boilerplate code ([SRE-752](https://allmyhomes.atlassian.net/browse/SRE-752))

**Changed:**

- update `allmyhomes/laravel-ddd-abstractions` from `^2.4` to `^2.4.1` ([SRE-752](https://allmyhomes.atlassian.net/browse/SRE-752))

## [v3.4.0] - 2020-10-29

**Added:**

- abstract base test for using projections in scenario tests

**Changed:**

- switch gitlab pipeline configuration to [devops k8 pipeline repository](https://gitlab.smartexpose.com/allmyhomes/devops/pipelines)
- update `allmyhomes/php-event-projections` from `v1.2.x` to `v1.3.0`
- update `event-engine/php-data` from `v0.1.2` to `v1.1.0`

**Fixed:**

- support deep nesting in snake case event payload translator [SRE-530](https://allmyhomes.atlassian.net/browse/SRE-530)

## [v3.3.0] - 2020-08-28

**Added:**

- Add "Composer Dependency Validation" GitLab-CI job in `pre-test` stage
- Add Blackfire to dockerfile to profile service ([Guide](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/blob/master/docs/Boilerplate/Usage/blackfire.md))

**Changed:**

- Not specifying queue name and priority level in laravel-worker.conf
- Update `Boilerplate` setup for our local cluster
- Increase `PHPStan` to Level 6
- Include `tests/PHPUnit` in `PHPStan` checks

**Fixed:**

- Use `server.php` in dredd testing to urldecode in laravel application without real server
- Fix `ocurred_at` key naming in `MySqlOccurredAtStreamMigration`
- Fix pagination of next and previous page link to contain same query string as the request

**Deprecated:**

- Mark codeception/domain-assert package implementation as deprecated

## [v3.2.2] - 2020-07-29

**Changed:**

- Change composer instance of private composer packages to `composer.envs.io` - [more details](docs/Boilerplate/Usage/update-composer-server.md) important to read

**Fixed:**

- Security fix from Laravel with v6.18.27 - [details](https://laravel-news.com/important-laravel-security-updates)

## [v3.2.1] - 2020-07-06

**Fixed**:

- Use of Symfony HttpFoundation Response instead of Laravel Response when adding tracing id to the response object

## [v3.2.0] - 2020-06-26

**Added:**

- Ability to pass/generate tracing id `x-b3-traceid` and log it
- Logging in case of shutdown function triggered `logOnShutdown`
- Support to `snake_case` in ES/CQRS Boilerplate instead of `camelCase` - [more info](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/blob/master/docs/Boilerplate/Usage/apply-snake_case-to-es-event-payload.md)

**Changed:**

- Update PHP version to `7.4` from `7.3`

**Fixed**:

- Remove deprecated Abstraction creation except AbstractAction and AbstractActionContent ([SRE-64](https://allmyhomes.atlassian.net/browse/SRE-64)).
- Ability to reconstitute state from list of events if state object isn't present
- Add database services to phpunit pipeline
- Fix cronjob alerts to newrelic

## [v3.1.1] - 2020-06-18

**Fixed:**

- Use same database connection settings for testing and production

## [v3.1.0] - 2020-05-19

**Added:**

- Add GitLab CI job for `PHPUnit`
- Add GitLab CI job to generate the combined Codeception and PHPUnit coverage
- Add ImmutableEventTrait to add occurred_at to payload of events automatically ([SRE-191](https://allmyhomes.atlassian.net/browse/SRE-191))
- Add Support for the new Error and Exception structure - [more info](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/blob/master/docs/Boilerplate/Usage/error-exception-structure.md)
- Add Support for deprecating an endpoint - [more info](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/blob/master/docs/Boilerplate/Usage/api-deprecation.md)
- Add Support to include`occurred_at` in event payload for new and existing events - [more info](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/blob/master/docs/Boilerplate/Usage/add-occurred_at-to-existing-payload.md)

**Changed:**

- Update Codeception from `3.x` to `4.x`
- Update PHPUnit from `7.x` to `9.0`
- Update `allmyhomes/laravel-ddd-abstractions` from `2.2.0` to `2.3.0`
- Update `allmyhomes/php-codesniffer` from `1.2.1` to `1.3.0`
- Update `allmyhomes/php-event-projections` from `1.0.2` to `1.0.3`
- Restrict GitLab CI markdownlint job to `feature/*`, `fix/*`, `hotfix/*`, `release/`, `develop` and `master` branches
- Rename services, images, containers and envs by DevOps team

**Deprecated**:

- Deprecate`Application\Core\Exceptions\Allmyhomes\AmhExceptionsHandler` as `Application\Core\Exceptions\ExceptionRenderer` should be used instead

**Fixed**:

- Fix inconsistent pagination links attribute type. Before, when there was just one page, the links attribute was an empty array instead of object. ([SRE-81](https://allmyhomes.atlassian.net/browse/SRE-81))

## [v3.0.1] - 2020-04-03

**Fixed**:

- Fix `.gitlab-ci` for different php versions for codeception tests

## [v3.0.0] - 2020-04-02

**Added**:

- Insights on each deployment to newrelic with boilerplate version, boilerplate type and php version
- Add `allmyhomes/php-event-projections` package v1.0.0

**Changed**:

- Update Laravel Framework - from `v5.8.*` to `v6.x`
- Update `.gitlab-ci` to apply `Contract First` approach by specifying 2 jobs for `Static` and `Integration` contract validation
- `Dredd` testing is enabled only for develop, master and release branches
- Move Events Projectors to produce and consume events to a separate package `allmyhomes/php-event-projections`
- Upgrade `PHPStan` to Level 2
- Changed in permissions in `.ops/docker/Dockerfile` to use `root` and `www-data`
- Codesniffer updates by checking all `tests` folder

**Fixed**:

- Avoid spamming of exception 'Already running projections'

## [v2.2.0] - 2020-03-04

**Added**:

- Added `roave/security-advisories` to ensure secure composer packages ([SRE-105](https://allmyhomes.atlassian.net/browse/SRE-105))
- Added artisan command to stop producing and consuming projections ([SRE-84](https://allmyhomes.atlassian.net/browse/SRE-84))
- Added `markdownlint` to check our md files
- Added Gap Detection in Producing and Consuming Events Projections to avoid any skipped events

**Changed**:

- Use `codeception/domain-assert` via composer instead copy/pasted version in `tests/_support/DomainAssert`
- **BREAKING:** Pipeline `API contract validation` is not allowed to fail anymore ([SRE-85](https://allmyhomes.atlassian.net/browse/SRE-85))
- Upgrade of `allmyhomes/contract-utils` to `v2.1.0` from `v1.3.2`
- Upgrade of `prooph/pdo-event-store` to `v1.12.0` from `v1.11.0`

**Deprecated**:

- Deprecate `CustomJsonSerializer` and requires usage of `\League\Fractal\Serializer\DataArraySerializer`

**Fixed**:

- Aggregate version should increase for each event

## [v2.1.0] - 2020-01-31

**Added**:

- Added `Logging` with specific context attributes when producing or consuming events
- Added middleware to check if `{id}` in the url matches the `id` in `JWT Token` so logged in user can access and edit only his data, if the middleware is activated

**Changed**:

- Updated `EventServiceProvider` class from Laravel 5.8 and extend with `subscribe` property to register subscriber classes
- Replaced `.phpcs.xml` with `.phpcs.xml.dist`

**Fixed**:

- Fixed artisan schedule command for producing/consuming
- Fixed permissions of the cronjob to be `www-data` instead of `root`
- Fixed generated migration files by replacing `-` to `_` in application name

## [v2.0.1] - 2020-01-07

**Changed**:

- Replace abandoned `phpstan/phpstan-shim` with suggested `phpstan/phpstan` package

**Removed**:

- `allmyhomes/laravel-distributed-transactions` package is removed
- `Allmyhomes\NestedModules\` removed from psr-4 autoload

**Fixed**:

- Fix `error_reporting()` in `SuppressExceptions` to pass `integer` instead of `string` from environment variables
- Increase `memory_limit` for `phpstan/phpstan` package in `.gitlab-ci.yml` and `composer.json` script to avoid memory overflow issues

## [v2.0.0] - 2019-12-27

**Added**:

- Integrated `Prooph MultiModelStore` with its dependencies packages `event-engine/php-data`, `event-engine/php-persistence`, `event-engine/php-postgres-document-store` and `event-engine/prooph-v7-event-store`
- `EventSourcingController` should be used a base controller for Event Sourcing Modules
- Event Sourcing Infrastructure including ImmutableState, Command Interface, AbstractAggregateRoot, and MultiModelStoreAggregateRepository
- Collection Migration could be used to create collections by extending `CollectionMigration`
- Integrated Boilerplate with Postgres, could be used by merging [Postgres branch](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/tree/feature/integrate-postgres-from-boilerplate-v2) inside your service

**Changed**:

- Upgraded `PHP` version in `composer.json` to `PHP 7.3` from `PHP 7.1.3`

## [v1.8.1] - 2019-12-20

**Added**:

- Artisan Command `art projector:consuming:start {projectionName}` to start consuming events from the EventStore
- Artisan Command `art projector:producing:start {projectionName}` to start producing events to the EventStore
- Integrate `Prooph EventStore` for Local and Shared EventStore
- Module Generators for Modules with type `entity_event` to extend the right `EntityEventRepository` and stream migration files
- Codeception checks using PHP 7.4 in `.gitlab-ci` to easily integrate services to use PHP 7.4
- Cover `database/migrations` folder with PHP CodeSniffer rules

**Changed**:

- Module Generator command will require a module type (entity or entity_event) `art api:module:generate Test entity`
- Add Concurrency Exception handling in `AmhExceptionsHandler`
- Add Postgres Runtime Exception handling in `AmhExceptionsHandler` for Unique Violation
- Upgrade of `allmyhomes/contract-utils` to `v1.3.2` from `v1.3.1`
- Upgrade of `allmyhomes/laravel-ddd-abstractions` to `v2.0.0` from `v1.2.0`
- Updated `EventStreamMigration` abstraction to accept category and metadata as well as validate stream name

**Fixed**:

- Use of `expectThrowable` instead of deprecated function `expectException`
- Codesniffer syntax by adding empty line to the end of all files

## [v1.8.0] - 2019-11-25

**Added**:

- Configuration, composer packages and gitlab-ci job for dredd contract testing ([T7265](https://phabricator.envs.io/T7265))
- Laravel Dump Server added as dev-dependency package [Github](https://github.com/beyondcode/laravel-dump-server)
- `PHPStan` static analysis tool with lowest level 0 to composer packages and gitlab-ci jobs [Github](https://github.com/phpstan/phpstan)

**Changed**:

- Upgrade of `allmyhomes/contract-utils` to `v1.3.1` from `v1.2.0`
- Upgrade of `allmyhomes/contract-mock` to `v1.1.0` from `v1.0.1`
- Upgrade of `allmyhomes/laravel-ddd-abstractions` to `v1.2.0` from `v1.1.0`
- Moved laravel worker to log from a file to docker standard output to be accessible in kibana
- Updated `Application/Core/config/services.php` from Laravel 5.8 services by updating `SES` and `Mailgun` configuration
- Package `nunomaduro/collision` moved to be a dev-dependency package and upgraded to `v3.0` [Github](https://github.com/nunomaduro/collision)
- Pipeline `API contract version` is not allowed to fail anymore (**[T7697](https://phabricator.envs.io/T7697)**)

**Removed**:

- Removed `Stripe` configuration from `Application/Core/config/services.php`

**Fixed**:

- Codesniffer formatting was fixed in set of files

## [v1.7.2] - 2019-11-15

**Fixed**:

- Fix security issue in `MimeTypeGuesser` from `Symfony`

## [v1.7.1] - 2019-10-23

**Changed**:

- Replaced [`cakephp/cakephp-codesniffer`](https://github.com/cakephp/cakephp-codesniffer) with [`allmyhomes/php-codesniffer`](https://gitlab.smartexpose.com/allmyhomes/php-codesniffer) ([T8179](https://phabricator.envs.io/T8179))
- Persist Auth Token generated for service to service communication ([T8990](https://phabricator.envs.io/T8990))
- Different `Logging` channels to be used with `ELK` in `Application/Core/config/logging.php`

**Fixed**:

- Fix codesniffer syntax in `Stubs->RepositoryCest.stub` when generating a new module ([T8498](https://phabricator.envs.io/T8498))

## [v1.7.0] - 2019-09-03

**Added**:

- Automatically pass payload to application service ([T5426](https://phabricator.envs.io/T5426))
- Returns `406` status code in case of non-existing route ([T5044](https://phabricator.envs.io/T5044))
- `Codeception v3` is used instead of `allmyhomes/codeception` package ([T7081](https://phabricator.envs.io/T7081))

**Changed**:

- Update Laravel Framework - from `v5.7.*` to `v5.8.34` **[Migration Guide: Boilerplate v1.7](docs/migration-guide-1.7.0.md)** ([T7081](https://phabricator.envs.io/T7081))
- Update `Carbon` - from `v1` to `v2` **[Migration Guide: Boilerplate v1.7](docs/migration-guide-1.7.0.md)** ([T7080](https://phabricator.envs.io/T7080))
- Update `restrictAccess()` method definition in stubs ([T5426](https://phabricator.envs.io/T5426))
- Update `allmyhomes/contract-utils` package to version `1.2.0`
- Update `allmyhomes/laravel-ddd-abstractions` package to version `1.1.0`
- Update `.gitlab-ci` to include `API Contract Validation`
- Move Unit test files to have same folder structure as their implementation

**Removed**:

- `allmyhomes/codeception` is removed as it's outdated package ([T7081](https://phabricator.envs.io/T7081))

**Fixed**:

- Fix codesniffer in `CustomAssertionTest`
- Change database name of `test` environment in `.gitlab-ci` by editing `/.ops/start.sh`

## [v1.6.1] - 2019-07-01

**Added**:

- Added `AMH_BOILERPLATE_VERSION` in `.env.example` so it's easier to know which boilerplate version is used in your service

**Fixed**:

- Disable automatically adding throttle middleware to all api endpoints
- AmhExceptionsHandler class properly handles exception of type ThrottleRequestsException
- Update `jwt.php` to have `local` as default `APP_ENV` if `.env` file isn't set

## [v1.6.0] - 2019-06-18

**Changed**:

- Remove DingoAPI
- Remove DingoAPI-Blueprint
- Upgrade of AMH Laravel DDD Abstraction Package to `v1.0.3`
- Upgrade of AMH Laravel Token Verification Package to `v4.1.1`
- Use of `OAuthKeyHelper` instead of `jwt()` in `jwt.php`

## [v1.5.0] - 2019-05-06

**Added**:

- ADDED: AMH Laravel Access Token Faker `v1.0` (**[T5099](https://phabricator.envs.io/T5099)**)
- ADDED: AMH Laravel DDD Abstraction Package `v1.0` (**[T5363](https://phabricator.envs.io/T5363)**)
- ADDED: Healthz check endpoint (**[T4284](https://phabricator.envs.io/T4284)**)
- AbstractRepository
  - ADDED: `createOrUpdate()` method
- ADDED: unit tests for `AbstractRepository` and `AbstractService`

**Changed**:

- UPDATED: .env file with new micro services urls
- UPDATED: Laravel Framework - from `v5.6.*` to `v5.7.*` **[Migration Guide: Laravel v5.6 to v5.7](docs/how-to-update-boilerplate.md)**, (**[T4459](https://phabricator.envs.io/T4459)**)
- UPDATED: Laravel UUID - from `v1.2` to `v1.2.6` (**[T4459](https://phabricator.envs.io/T4459)**)
- UPDATED: AMH Laravel Token Verification - from `v3.0.1` to `v4` (**[T5397](https://phabricator.envs.io/T5397)**)
- UPDATED: Laravel Stats - from `v1.7.4` to `v2`
- AbstractRepository
  - UPDATED: refactored `whereInMultiple()` method (**[T3415](https://phabricator.envs.io/T3415)**)

**Deprecated**:

- AbstractRepository
  - DEPRECATED Classes (moved to [DDD-Abstraction package](https://gitlab.smartexpose.com/allmyhomes/laravel-ddd-abstractions)):
    - AbstractDomainService
    - AbstractFormRequest
    - AbstractRepository
    - AbstractRequest
    - AbstractTransformer

**Fixed**:

- AbstractRepository
  - FIXED: regular expression in `search()` method (**[T3415](https://phabricator.envs.io/T3415)**)
  - FIXED: `NotFoundException`, was throwing `500` (**[T4449](https://phabricator.envs.io/T4449)**)
- FIXED: fixed automatic generated tests when generating new module
