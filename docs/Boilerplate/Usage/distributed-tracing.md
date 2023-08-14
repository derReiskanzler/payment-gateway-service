# Distributed Tracing

## What is Distributed Tracing

Your system becomes distributed. With known profits comes known drawbacks.
Every little part of your system now requires independent monitoring. And the challenge is not only in different sources and formats of produced metrics and logs - it is now a challenge to easily find out, which unit of a huge infrastructure produced results that ended up with error in User experience.

But nothing is impossible, as well as bringing back monolithic overview on distributed system health.

Distributed Tracing - is a simple architectural concept, allowing to join distributed system monitoring into seamless process,
so basically by passing the same id within your request lifecycle and logging it, you will be able to easily figure the path of your request.

## How to use

### Front-end to Back-end

When a front-end application sends a request to back-end service, it can pass the following tracing id in request headers.

```http request
--header 'x-b3-traceid: f58rvxd6son0g9cddusylr1mvhu37kc2'
```

Trace Id should follow these rules:

- It's 32 characters length
- It's composed of [a-z] and [0-9]

Once it's sent then this trace Id will be the same in the request lifecycle and will be automatically logged
whenever logging used under `extra` key as well as visible in Kibana.

As well as the front-end could use the same key again in next request in order to track a complete process, if needed.