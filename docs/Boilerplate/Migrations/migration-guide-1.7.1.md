# v1.7.1 Migration Guide

This document outlines the changes applied in Boilerplate **v1.7.1** from **v1.7.0**.
> v1.7.1 is NOT backward-compatible with v1.7.0. It requires changes regarding only codesniffer.
> But It contains Medium and Low Impact changes.

To upgrade to v1.7.1, please follow this [guide](./boilerplate-migration.md)

## Medium Impact Changes

### Token Persistence

Back-end service to service communication requires `Token` which is requested from `Auth` service. At the moment, we cache this `Token` and use it as long as it's not expired instead of requesting a new `Token` for each request.

In general, this change should improve the performance, decrease latency and decrease the load on `Auth` service.

**Note**: Requires `no changes` to your code base.

### Allmyhomes PHP Codesniffer

Replacement from `cakephp/cakephp-codesniffer` to `allmyhomes/php-codesniffer`.
Currently, It applies the same rules as `cakephp/cakephp-codesniffer` .

**Note**: Requires `changes` to your code base.
Running `composer cs-fix` could help to auto fix some of code sniffer issues.

## Low Impact Changes

### Logging

Added different logging channels to be also shown on the docker output.

**Note**: Requires `no changes` to your code base.

### Module Generation Codesniffer update

Fix codesniffer syntax in `Stubs->RepositoryCest.stub`

**Note**: Requires `no changes` to your code base.
