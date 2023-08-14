# v3.5.0 Migration Guide

This document outlines the changes applied in Boilerplate **v3.5.0** from **v3.4.x**.
> v3.5.0 is backward-compatible with v3.4.x.

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Changes](#changes)
  - [High Impact Changes](#high-impact-changes)
  - [Medium Impact Changes](#medium-impact-changes)

## Upgrade Guide

To upgrade to v3.5.0, please follow this [guide](./boilerplate-migration.md)

## Changes

### High Impact Changes

#### Producing/Consuming local events on-demand

We provide a new mechanism to produce or consume local events which is on-demand and highly recommended to use.
For more info on how to use it, could chek full description[here][1].

#### Laravel Config Caching

Configuration caching is recommended as one of Laravel Optimization techniques. It's highly recommended to enable it.

If order to run config caching on the service, please check the [documentation][2].

**Note**: might requires `changes` to your code base.

### Medium Impact Changes

#### Laravel Route Caching

Route caching is recommended as one of Laravel Optimization techniques. It's enabled in our docker file.

**Note**: requires `no changes` to your code base.

[1]: <https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/blob/master/docs/Boilerplate/Usage/produce-consume-events-shared-eventstore.md>
[2]: <https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/backend/boilerplate/Laravel-Optimizations.md>
