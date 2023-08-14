# v1.7.2 Migration Guide

This document outlines the changes applied in Boilerplate **v1.7.2** from **v1.7.1**.
> v1.7.2 is backward-compatible with v1.7.1. It contains security fix from Symfony.

To upgrade to v1.7.2, please follow this [guide](./boilerplate-migration.md)

## Low Impact Changes

### Security Fix by Symfony

Symfony found a security issue in provided file path in `MimeTypeGuesser` and they fixed it in v4.3.8 - [Symfony Issue](https://symfony.com/blog/cve-2019-18888-prevent-argument-injection-in-a-mimetypeguesser?utm_source=feedburner&utm_medium=feed&utm_campaign=Feed%3A+symfony%2Fblog+%28Symfony+Blog%29)

**Note**: Requires `no changes` to your code base.
