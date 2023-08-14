# v2.2.0 Migration Guide

This document outlines the changes applied in Boilerplate **v2.2.0** from **v2.1.0**.
> v2.2.0 is NOT backward-compatible with v2.1.0.

## Table of Content

- [Upgrade Guide](#upgrade-guide)
- [Changes](#changes)
  - [High Impact Changes](#high-impact-changes)
  - [Medium Impact Changes](#medium-impact-changes)
  - [Low Impact Changes](#low-impact-changes)

## Upgrade Guide

To upgrade to v2.2.0, please follow this [guide](./boilerplate-migration.md)

If you didn't upgrade to v1.8.1 and directly upgrading to v2.2.0, please follow the [Upgrade Guide](migration-guide-1.8.1.md) of v1.8.1 first

## Changes

### High Impact Changes

#### API contract validation strictly validate

Starting from Boilerplate v2.2.0, we started to strictly validate your contract.

It's required to write a valid contract for each of service to pass the pipeline.

**Note**: Requires `changes` to your code base.

### Medium Impact Changes

#### Kibana Logstash reads our logs from kibana.log file

Before Kibana had access to supervisord logs which wasn't handling all cases like cron jobs.

We changed the logic that logstash read from `src/Infrastructure/Boilerplate/Laravel/storage/logs/kibana.log` instead of directly `php://stdout` which give us the ability to forward all logs from different php processes to kibana.

**Note**: requires `no changes` to your code base.

#### Allow only DataArraySerializer and deprecate CustomJsonSerializer

We worked on a bug fix related to our `Array Serializer` and decided to work only with `DataArraySerializer` and deprecate the `CustomJsonSerializer`.

**Note**: Might requires `changes` to your code base but in general shouldn't.

#### Use Codeception/domain-assert from package instead of hardcoded file

We use `codeception/domain-assert` package in require-dev instead of copy of the file.

**Note**: Might requires `changes` to your code base but in general shouldn't unless you changed the code in your service.

#### OAuth Cache file name is stored in environment variable

We moved the `OAUTH Cache file` to get the name of the file from environment variables so it's easy to change the name between different environments.

**Note**: requires `no changes` to your code base.

### Low Impact Changes

#### Added Artisan command to stop projections

We included artisan command to stop producing or consuming projections from running so ensure safe shutdown for projections.

The commands are:

- `art projector:consuming:stop {projectionName}` - stops a specific consuming projection
- `art projector:all:consuming:stop` - stops all consuming projections
- `art projector:producing:stop {projectionName}` - stops a specific producing projection
- `art projector:all:producing:stop` - stops all producing projections

**Note**: Requires `no changes` to your code base.

#### Added Markdown lint

We included a package `pipelinecomponents/markdownlint` to validate our `.md` file.

You can run the check by running `composer markdownlint`

**Note**: Requires `changes` to your code base.
