# v1.8.0 Migration Guide

This document outlines the changes applied in Boilerplate **v1.8.0** from **v1.7.2**.
> v1.8.0 is NOT backward-compatible with v1.7.2.
> It contains breaking changes regarding contract testing and CodeSniffer Syntax

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Core Functionality](#core-functionality)
- [Changes](#changes)
  - [Medium Impact Changes](#medium-impact-changes)
  - [Low Impact Changes](#low-impact-changes)

## Upgrade Guide

To upgrade to v1.8.0, please follow this [guide](./boilerplate-migration.md)

**Steps**:

- Re-run pipelines on `develop` since need to have a new base image
- Update your `cluser` setup from `master` branch
- Install new chart in your cluster by running `bin/install charts/event-store/`
- Use any Postgres GUI, for example OmniDB or any other GUI you prefer
- Connect to your local event-store with server `api.dev.local`, port `31578` and username/password same as always and username/password same as always and should have 2 databases (event-store, event-store_test)

**Hint**: If `event-store_test` wasn't created, please run this command and update SRE team

```shell
docker run postgres:11-alpine sh -c "export PGPASSWORD='password'; psql -h '$(minikube ip)' -p 31578 -d 'event-store' -U 'root' -c 'CREATE DATABASE \"event-store_test\";'"
```

- Update your `Models` to extend `AbstractModel` instead of `Laravel Model`

## Changes

### Medium Impact Changes

#### DDD Abstraction Package

There was a new feature added to `DDD Abstration` package. Currently, we support persistence of events with entities.
It's backward-compatible changes. Just need to update the extension of `AbstractModel` instead of `Model`.

If would like to use the new feature:

- You need to have the latest cluster setup
- Install new chart in your cluster, run `bin/install charts/event-store`
- Use any Postgres GUI, for example OmniDB or any other GUI you prefer
- Connect to your local event-store with server `api.dev.local`, port `31578` and username/password same as always and should have 2 databases (event-store, event-store_test)

**Hint**: If `event-store_test` wasn't created, please run this command

```shell
docker run postgres:11-alpine sh -c "export PGPASSWORD='password'; psql -h '$(minikube ip)' -p 31578 -d 'event-store' -U 'root' -c 'CREATE DATABASE \"event-store_test\";'"
```

**Note**: Requires `changes` to your code base.

#### Dredd Testing

Dredd Testing is added to `composer.json` command and in `.gitlab-ci` with allow to failure until the team adapt their dredd testing

**Note**: Requires `changes` to your code base.

#### Contract Testing

There was 2 packages updates `allmyhomes/contract-utils` and `allmyhomes/contract-mock`.
It's more strict and provide proper error log with these updates.

**Note**: Requires `changes` to your contracts.

#### PHPStan

PHPStan is a static analysis tool. It's added to `composer.json` command and in `.gitlab-ci` with allow to failure until the team adapt their analysis tool.
Currently the level used is the lowest `0` and will be upgraded step by step in next releases.

**Note**: Requires `changes` to your code base.

### Low Impact Changes

#### Codesniffer Version Upgrade

CakePHP Codesniffer released a new version `3.1.2`.

**Note**: Requires `changes` to your code base but `composer cs-fix` could help.

#### Codesniffer fix in boilerplate

Fixed some Syntax in boilerplate files.

**Note**: Requires `no changes` to your code base.
