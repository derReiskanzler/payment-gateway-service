# v2.0.0 Migration Guide

This document outlines the changes applied in Boilerplate **v2.0.0** from **v1.8.1**.
> v2.0.0 is backward-compatible with v1.8.1.

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Changes](#changes)
  - [Low Impact Changes](#low-impact-changes)

## Upgrade Guide

To upgrade to v2.0.0, please follow this [guide](./boilerplate-migration.md)

If you didn't upgrade to v1.8.1 and directly upgrading to v2.0.0, please follow the [Upgrade Guide](migration-guide-1.8.1.md) of v1.8.1 first

## Changes

### Low Impact Changes

#### Event Sourcing Infrastructure

In Boilerplate v2.0.0, we integrated `Prooph MultiModelStore` concepts by having the classes required inside our Infrastructure Layer.

This Infrastructure is essential in case a service start to work with `Aggregates`, `ValueObjects`, `ImmutableState` and completely event driven architecture without any persistence of events.

**Note**: Requires `no changes` to your code base.
