# How to migrate service to Event Sourcing

In Boilerplate v2.0.0, we integrated `Event Sourcing` infrastructure in the boilerplate as a step forward to teams to start upgrading service implementation to be events driven.

Of course, It's not an easy step specially for existing services that's why we thought to have a sort of migration guide.

**Important**:

- First of all, the service need to be on Boilerplate v2.0.0+
- With our current implementation of `MultiModelStore`, it's `Postgres` dependent, so it's required to migrate your service to use postgres by merging [this branch](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/tree/feature/integrate-postgres-from-boilerplate-v2)

## 1st Migration Step - Migrate to Postgres

Currently, we have an existing service that is running on `live` and `stable` with `MySQL` DB.

So the first step would be migration to `Postgres`, in order to do that:

- It's required to migrate the service to use postgres by merging [this branch](https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/tree/feature/integrate-postgres-from-boilerplate-v2)
- It's required to test the service behaviour locally using `Postgres` by running automation and manual tests
- Please contact `DevOps` team to prepare service DB using `Postgres`
- We need to create migration scripts to migrate data from `Mysql` to `Postgres`
- Please test your service heavily on `Develop` and `Staging` servers
- There will be some **downtime**, please communicate `DevOps` and `SRE` teams to plan the migration together.

## 2nd Migration Step - Event Sourcing inside your Module

As we are done from 1st Migration Step and only after service is `stable` on `live`, then we can start to migrate to Event Sourcing structure.

- You can start to create a new module following `Event Sourcing` structure
- You can start to migrate module per module instead of heavy migration
  - It's required to prepare a migration for the current entity state into event payload
  - It might be required to have `v1` and `v2` of your endpoints to not break service consumers
  - It's required to rebuild your `Read model` state from your events history
- It's requited to test service heavily after every change
- There will be some **downtime** depending on how big the change is, how big the module is. Please communicate with `DevOps` and `SRE` teams.

*Please note* This guide isn't final, we will keep it up to date once we figure out new cases.
