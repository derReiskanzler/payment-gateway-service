# Sentry

[Sentry] is an Application Monitoring and Error Tracking Software.

From error tracking to performance monitoring, developers can see what actually matters, solve quicker, and learn continuously about their applications - from the frontend to the backend.

## Sentry for Laravel

Laravel is supported via a native package, [Sentry Laravel].

We are using the default configuration as configured in [Sentry Laravel] with 2 changes:

- SENTRY_TRACES_SAMPLE_RATE is set to `1` - Enable performance monitoring
- SENTRY_TRACE_QUEUE_ENABLED is set to `true` - Add tracing support for queue jobs

Other environment variables like:

- `SENTRY_LARAVEL_DSN`
- `SENTRY_ENVIRONMENT`
- `SENTRY_RELEASE`

will be taken care by DevOps team during delivery process.

## How to Adopt Sentry

- Please adopt the service to the latest boilerplate version 3.8.x+
- All the needed environment variables are already included in all services by DevOps Team. If something is wrong with the above environment variables please contact DevOps Team
- Deploy and start to see the `issues` on <https://sentry.infrastructure.envs.io/>

[Sentry]: <https://sentry.io/welcome/>
[Sentry Laravel]: <https://github.com/getsentry/sentry-laravel>
