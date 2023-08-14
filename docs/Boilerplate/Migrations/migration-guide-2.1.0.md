# v2.1.0 Migration Guide

This document outlines the changes applied in Boilerplate **v2.1.0** from **v2.0.1**.
> v2.1.0 is backward-compatible with v2.0.1.

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Changes](#changes)
  - [Medium Impact Changes](#medium-impact-changes)
  - [Low Impact Changes](#low-impact-changes)

## Upgrade Guide

To upgrade to v2.1.0, please follow this [guide](./boilerplate-migration.md)

If you didn't upgrade to v1.8.1 and directly upgrading to v2.1.0, please follow the [Upgrade Guide](migration-guide-1.8.1.md) of v1.8.1 first

## Changes

### Medium Impact Changes

#### Log Producing & Consuming Events

Starting from Boilerplate v2.1.0, we started to automatically log the produced and consumed events, as well as if there is an error happened during producing or consuming process.

All the logs has specific contexts so they can be filtered properly in Kibana and have separate dashboards.

**Note**: Requires `no changes` to your code base.

#### DDD-Abstraction Package shows better exceptions

We adjusted the `DDD-Abstraction` package to show a better view of exception so it's easier to debug.

**Note**: Requires `no changes` to your code base.

### Low Impact Changes

#### Include EventId in EventDTO

We directly pass `Event UUID` to Event Data Transfer Object and Event Mapper Abstract.

It can be useful to pass it as `causation id` between commands and keep track of single process that involves multiple commands/events.

**Note**: Requires `no changes` to your code base.

#### Verify User Id in Url with JWT Token

In Boilerplate v2.1.0, we included a middleware `VerifyJwtUserId` that verifies if the `{id}` passed in the url matches the logged-in user.

This middleware can be activated per group or single endpoint(s).

**Note**: Requires `no changes` to your code base.
