# Update PHP version locally

From time to time, it's required to update our php version and that's
require checking and running all needed tests, linters before pushing the changes.

## How to update PHP version?

All a service should update to the latest Boilerplate version which contains the changes
in `Dockerfile` and composer dependencies.

## How to test locally?

There are several ways to test the changes locally and in this guide, we will present some of them and feel free
to chose easiest & preferable way.

### Docker Compose

One of the easiest way is to use `docker-compose`. It's so easy to rebuild the service base image with latest changes in
`Dockerfile`.

1. To apply this way, please make sure that the service docker-compose file is up to date with the version of the boilerplate
to have it running smoothly.

2. On the root path of the project, please run `docker-compose up --build`

### Minikube

Our minikube relies on pulling the latest remote image with tag `local` and `local-testing`,
that's why it's not possible to adapt the changes in the `Dockerfile` to already built image.

We have several solutions and feel free to pick your prefered one.

#### Use Boilerplate Image

Since Boilerplate already upgrade to use latest PHP version then we could use it as base image
and mount our service code on top of it.

1. Run `bin/install charts/be-laravel-boilerplate`

2. Run `bin/switch2devel charts/be-laravel-boilerplate ../service-code-base`

3. Run `bin/shell service`

4. Run `php -v` and should be already running on the expected PHP version and feel free to test and validate the changes

#### Push The code

Another possible way is to push the changes remotely.

1. Push the changes blindely

2. Get pipelines to work

3. Deploy to develop

4. On cluster-setup, re-install the service

5. Run `bin/shell service`

6. Run `php -v` and should be already running on the expected PHP version and feel free to test and validate the changes
