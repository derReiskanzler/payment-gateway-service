# Merge Checklist

Before asking your teamlead to merge something into `develop`, please ensure that all (relevant) points of this
checklist where considered.

## Changelog

- [ ] mentioned all changes in `CHANGELOG.md`
- [ ] each change references its ticket(s) in format `(**[<number>](https://phabricator.envs.io/<number>)**)`
- [ ] all changes are categorized either as `Added`, `Changed`, `Deprecated`, `Fixed` or `Removed`
- [ ] all changes are below the version tag `[UNRELEASED]` and **NOT** below a tag for the next version

## Api-Contract

- [ ] all changes to endpoints are reflected in the api-contract
- [ ] the version number is **NOT** adjusted, this will be done by your lead while releasing

## Tests

- [ ] all changes have according tests
- [ ] no significant drops in code coverage

## Scopes

- [ ] all added/changed scopes exist already or are already confirmed to be implemented by the [Auth-Service] (which is currently maintained by [BAT])
- [ ] all scopes are in the format described in the [Scope Documentation]: mostly `<service>:<module>:read_write`, whereby the module name is pluralized.

## Review

- [ ] all changes were reviewed according to your teams review process. (Example: BATs changes must be reviewed by at least two people)
- [ ] if you implement a solution for a `DEP`-Ticket, it should also be reviewed by the dependency-team

## Licenses

- [ ] if new composer packages are used, the License must be checked to comply with following conditions (check the licenses at <https://tldrlegal.com/license>):
  - always:
    - `allowed for Commercial Use`
  - in production dependencies (= NOT require-dev):
    - `allowed to sublicense`
  - if we need to make changes:
    - `allowed to modify`

## No Trickery of Quality Tools

- [ ] no `phpcs ignore`, `@codeCoverageIgnore` or similar exclusions, except there is really a GOOD reason for it. *("But it's not written by us" is NO good reason!)*

[Please put all urls below this section and use reference-style to keep the document readable in plain-text-view]: -

[Auth-Service]: https://gitlab.smartexpose.com/allmyhomes/laravel-auth-service
[BAT]: https://phabricator.envs.io/project/view/39/
[Scope Documentation]: https://gitlab.smartexpose.com/allmyhomes/technology/blob/master/AUTHENTICATION.md#scopes
