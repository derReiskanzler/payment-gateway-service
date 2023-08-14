# v3.7.0 Migration Guide

This document outlines the changes applied in Boilerplate **v3.7.0** from **v3.6.0**.

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Changes](#changes)

## Upgrade Guide

To upgrade to v3.7.0, please follow this [guide](./boilerplate-migration.md)

In case you have implemented the v0.0.1 of the Keycloak Package beforehand please
note that we have changed the guard name in laravel to `keycloak`.

Dismiss your changes to `auth.php` in favor of the changes in this update and
change the middleware in your routes from `auth:api` to `auth:keycloak`.

This was done to retain the functionality of the old TokenVerificationPackage.

## Changes

This update only adds a single new feature:
The [Laravel Keycloak Guard Package](https://gitlab.smartexpose.com/allmyhomes/laravel-keycloak-guard-package/).

It is wrapped into a boilerplate-update as we also had to adjust the ExceptionRenderer.

If you don't need the Keycloak Guard Package you can simply skip this update.
