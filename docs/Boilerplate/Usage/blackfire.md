# Profiling the Application (API and CLI)

- [Profiling an API](#profiling-an-api)
- [Profiling CLI](#profiling-cli)

## Profiling an API

### How does it work

- You need to have Blackfire container (agent) running inside your network (whether it is local docker-compose or, for example, staging) and php extension installed in the service you're going to profile (see Dockerfile for details);
- You need to have your request prepared as CURL (Postman is a great tool to help with it);
- Now you're sending this CURL (embedded for blackfire) to blackfire agent;
- Agent will add some "magic" to your request and will send it to mentioned service (X times);
- Service will receive the request and enable blackfire profiling procedure (this happens only if agent is used, so this feature **does not have impact on performance** unless explicitly enabled - means it can freely be deployed to production);

### Local API profiling

For **local** profiling of an API call, use `docker-compose-profile.yaml` with your docker-compose.

It will build same group of containers but with one extra - [blackfire](https://blackfire.io).

To build it you'll need to use 4 env variables:

- `BLACKFIRE_CLIENT_ID`
- `BLACKFIRE_CLIENT_TOKEN`
- `BLACKFIRE_SERVER_ID`
- `BLACKFIRE_SERVER_TOKEN`

These variables can be found in your (or team shared) [blackfire account](https://blackfire.io/my/settings/credentials).
If you do not have account - just register one, it is free. And you can share these credentials - there's no actual value in their secrecy unless you have a paid account.

Data, collected during profiling, **will be sent to 3rd party**, calculated and transformed into visualization chart. Later this chart can be publicly shared.

Now, run:

```shell
docker-compose -f ./docker-compose-profile.yaml exec blackfire blackfire curl boilerplate-app
```

First `blackfire` stands for container name, second - for cli command.

We're using `boilerplate-app` instead of `localhost` here, because from the point of `blackfire` container `localhost` is himself.

Pay attention to the name of your application container. Since blackfire agent is running from inside of docker network, it might not be able to resolve addresses like [http://127.0.0.1:80](http://127.0.0.1:80)

If everything is properly set up, you'll see something like this:

```shell
$ docker-compose -f ./docker-compose-profile.yaml exec blackfire blackfire curl boilerplate-app/healthz
Profiling: [###########################] 10/10
Blackfire cURL completed
Graph                 https://blackfire.io/profiles/32d4da12-a98b-47cb-a87b-a9fc092ce4b4/graph
No tests!             Create some now https://blackfire.io/docs/cookbooks/tests
No recommendations

Wall Time     1.15s
Memory       2.92MB
Network         n/a     n/a     n/a
SQL             n/a     n/a
```

Follow link to see result! 🎉
(You can make the result public and share it!)

### Profiling API of your application on remote environment

TODO

## Profiling CLI

Profiling of CLI is [different](https://blackfire.io/docs/integrations/docker/php-docker) from profiling an API request.

We shall need one additional binary to "wrap" local cli command with blackfire.

### Local CLI profiling

For **local** profiling of CLI, use `docker-compose-profile.yaml` with your docker-compose (same as for API profiling).

See [Local API profiling](#local-api-profiling) for setup instructions.

Now, run:

```shell
docker-compose -f ./docker-compose-profile.yaml exec boilerplate-app blackfire run php artisan
```

First `boilerplate-app` stands for container name, `blackfire run` - is our "wrapper" around actual cli command: `php artisan`.

### Profiling CLI of your application on remote environment

TODO

## Blackfire links and features

It is [production ready](https://blackfire.io/features#performance-testing) - meaning, there is no impact on performance. It only works "on demand".

Blackfire supports [distributed profiling](https://blackfire.io/docs/reference-guide/distributed-profiling#distributed-profiling).

Use multiple "samples" to increase accuracy of profiling: `blackfire --samples 10 curl http://google.com`

Blackfire [worked a lot with Symfony Foundation](https://symfonycasts.com/screencast/blackfire) to make profiling of Symfony applications really good.

You might want to **compare profiles**. For example "before" and "after". This is [totally possible](https://blackfire.io/docs/book/05-validating-performance-optimizations#step-4-comparing-profiles-code-changes)!

To understand better difference of Blackfire and, for example, New Relic - check [this article](https://blackfire.io/docs/book/03-what-is-blackfire).

----
