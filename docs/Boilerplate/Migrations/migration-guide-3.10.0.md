# v3.10.0 Migration Guide

This document outlines the changes applied in Boilerplate **v3.10.0** from **v3.9.x**.

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Changes](#changes)
  - [General Changes](#general-changes)
  - [MySql Specific Changes](#mysql-specific-changes)
- [Removed](#removed)

## Upgrade Guide

To upgrade to v3.10.0, please follow this [guide](../Usage/update-php-version.md) since we are also updating the PHP
version, you cannot do it with the cluster alone. Keep in mind to delete the cache files (located in
`Application/Core/bootstrap/cache`) since they contain information of classes which have been removed. 

## Changes

### General Changes

This update will introduce a few changes:
We increased our PHP version from version 7.4.x to version 8.0.x.

Please refer to [update php version guide](../Usage/update-php-version.md). It provides different ways to upgrade PHP and test locally.

New features include:
- Constructor properties
  - declaration of `public`, `protected` or `private` properties from within the constructors
- Union types (e.g. `public function getSomeStuff(): string|bool`)
- Nullsafe operator: `?->`
- added `str_contains()`, `str_starts_with()`, `str_ends_with()` methods
- for the entire list check the [official documentation](https://www.php.net/manual/en/migration80.new-features.php)

Incompatibilities include:
- The ability to call non-static methods statically has been removed.
- The ability to define case-insensitive constants has been removed.
- The ability to use `array_key_exists()` with objects has been removed. `isset()` or `property_exists()` may be used instead.
- Uncaught exceptions now go through "clean shutdown", which means that destructors will be called after an uncaught exception.
- for the entire list check the [official documentation](https://www.php.net/manual/en/migration80.incompatible.php)

### Laravel Specific Changes

We also replaced `emadadly/laravel-uuid` package with our own version: `allmyhomes/laravel-uuid`.

This requires a change to our classes which extend `AbstractModel` and previously declared the trait `Uuids`, you need
to change the import statement accordingly.  

## Removed

We removed the following packages:
- `beyondcode/laravel-dump-server`
