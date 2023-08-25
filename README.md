# Domain Driven Laravel API boilerplate

![Laravel Backend Boilerplate](https://laravel.com/assets/img/components/logo-laravel.svg "Laravel Backend Boilerplate")

----

### Features

#### Event Sourcing based

The boilerplate provides a mechanism as a process for Event Driven Architecture.

- [Complete ES/CQRS](docs/Boilerplate/Usage/event-sourcing-boilerplate-how-to-use.md)

#### Producing and Consuming Events

The boilerplate provides the infrastructure needed to produce and consume events to our shared EventStore.
A usage example could be [here](docs/Boilerplate/Usage/produce-consume-events-shared-eventstore.md)


### Mail Renderer Client

This service uses the [Mail Renderer Client](https://gitlab.smartexpose.com/allmyhomes/site-reliability-engineering-team/mail-renderer-client)  in order to send emails via the [Mail Renderer Client](https://gitlab.smartexpose.com/allmyhomes/site-reliability-engineering-team/mail-renderer-service).

Read more about the client and service & how it is set up [here](https://allmyhomes.atlassian.net/wiki/spaces/AC/pages/194249773/Distributed+Mail+System).



### References

- Architecture and Design Patterns
  - [Large scale Laravel Application](https://medium.com/@munza/large-scale-laravel-application-9d52c3d38e51)
    - [Transformers](https://medium.com/@haydar_ai/how-to-start-using-transformers-in-laravel-4ff0158b325f)
    - [Repository Pattern](https://medium.com/@sinsin_78919/the-repositories-pattern-in-lumen-75cf08145d96)
    - [Command Pattern](https://tactician.thephpleague.com)
  - [Lucid Architecture](https://github.com/lucid-architecture/laravel-microservice)
  - [PHP Design Patterns](http://designpatternsphp.readthedocs.io/en/latest/README.html)
  - [Observers](https://medium.com/@secmuhammed/create-command-for-laravel-observers-3a0a65582aa4)

- SOLID
  - [Single-action class](https://medium.com/@remi_collin/keeping-your-laravel-applications-dry-with-single-action-classes-6a950ec54d1d)
  - [Layers](https://medium.com/@ivelinpavlov/the-layers-attack-ca8750202b2e)

- Tutorials
  - [blueprint-dreed](https://hackernoon.com/writing-and-testing-api-specifications-with-api-blueprint-dreed-and-apiary-df138accce5a)
  - [api-documentation-tools](https://pronovix.com/blog/free-and-open-source-api-documentation-tools)

- Credentials
  - [encrypted .env](https://medium.com/@marcelpociot/a-env-replacement-for-storing-secret-credentials-in-your-laravel-application-fdbae6c9f41b)

- Links
  - [App Layers in Laravel](https://medium.com/@ivelinpavlov/the-layers-attack-ca8750202b2e)
  - [Separation of Concerns in Laravel](https://medium.com/@jon.lemaitre/separation-of-concerns-with-laravel-s-eloquent-part-3-collections-relations-eager-loading-and-e13530a8890a)
  - [DDD in Laravel](http://lorisleiva.com/conciliating-laravel-and-ddd)

- Patterns
  - [Design Patterns in PHP](https://github.com/domnikl/DesignPatternsPHP )
  - [Repository Pattern](https://medium.com/@sinsin_78919/the-repositories-pattern-in-lumen-75cf08145d96)
  - [Service Pattern](https://m.dotdev.co/design-pattern-service-layer-with-laravel-5-740ff0a7b65f)
  - [Container Pattern](http://container.thephpleague.com)
  - [Command Pattern](https://tactician.thephpleague.com)

- Versioning
  - [Microservice Versioning](https://blog.travelex.io/microservice-versioning-a75d34d575)
