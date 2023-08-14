# v3.0.0 Migration Guide

This document outlines the changes applied in Boilerplate **v3.0.0** from **v2.2.0**.
> v3.0.0 is NOT backward-compatible with v2.2.0.

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Changes](#changes)
  - [High Impact Changes](#high-impact-changes)
  - [Medium Impact Changes](#medium-impact-changes)

## Upgrade Guide

To upgrade to v3.0.0, please follow this [guide](./boilerplate-migration.md)

If you didn't upgrade to v1.8.1 and directly upgrading to v3.0.0, please follow the [Upgrade Guide](migration-guide-1.8.1.md) of v1.8.1 first

## Changes

### High Impact Changes

#### Update to Laravel 6

We upgraded to Laravel 6 with Boilerplate v3.0.0. Please check the [upgrade guide](https://laravel.com/docs/6.x/upgrade#upgrade-6.0).

**Note**: Requires `changes` to your code base.

So far we noticed those changes that might affect our services:

##### # Declaration Of Primary Key Type

Laravel 6.0 has received performance optimizations for integer key types.
If you are using a string as your model's primary key, you should declare the key type using the $keyType property on your model.

```php
/**
 * The "type" of the primary key ID.
 *
 * @var string
 */
protected $keyType = 'string';
```

##### # String & Array Helpers Package

All str_ and array_ helpers have been moved to the new laravel/helpers Composer package and removed from the framework.

Alternatively, you can add the new laravel/helpers package to your application to continue using these helpers: `composer require laravel/helpers`

##### # Localization

The `Lang::trans` and `Lang::transChoice` methods of the translator have been renamed to `Lang::get` and `Lang::choice`.

The `Lang::get` and `Lang::getFromJson` methods have been consolidated. Calls to the `Lang::getFromJson` method should be updated to call `Lang::get`.

##### # Queue Retry Limit

Before the php `artisan queue:work` command would retry jobs indefinitely. Beginning with Laravel 6.0, this command will now try a job one time by default.

##### # Input Facade

The `Input` facade, which was primarily a duplicate of the `Request` facade, has been removed.

All other calls to the `Input` facade may simply be updated to use the `Request` facade.

##### # Scheduling

The scheduler's `between` method changed it's parameters order because it was confusing before.

##### # Validation

FormRequest `validationData` Method is changed from `protected` to `public`.

So if you override this method, you should update the visibility to `public`.

##### # S3 Endpoint Url

If you are using S3, please note this github issue: <https://github.com/laravel/laravel/pull/5267>

#### Event Projections Package

We moved infrastructure of Events Projections to produce and consume events into a separate package `allmyhomes/php-event-projections`.

**For that, we have deprecated classes `EventDTO`, `EventMapper` and `EventHandlerInterface`, please change your extension to the right classes as proposed.**

**Note**: requires `changes` to your code base.

### Medium Impact Changes

#### Contract Testing in .gitlab-ci

Starting from v3.0.0, we start to follow `Contract First` approach by specifying 3 jobs:

- API Contract Static Validation -> check the contract statically
- API Contract Integration Validation -> check the integration between the contract and the service implemented
- API Contract Feature Integration Validation -> run on feature branches and allows to have contracts updated without implementation yet

**Note**: requires `no changes` to your code base.

#### PHPStan to Level 2

Many services are already on `PHPStan` Level 2 so it's important step to have the boilerplate on Level 2, so we don't produce problems to these services.

If your service isn't ready yet to upgrade to Level 2, feel free to change it to lower level and create a ticket to upgrade to Level 2.

**Note**: requires `changes` to your code base.

#### Deployment Insights

Starting with Boilerplate v3.0.0, we will pass some insights to `NewRelic` regarding the boilerplate version, boilerplate type and php version that the service is using.

**Note**: requires `no changes` to your code base.
