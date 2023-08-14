# Update composer server

As we are changing our private composer server to `composer.envs.io`, it's important to update our services with the new server
info before we shutdown the old server.

## How to migrate

- Please upgrade your service to v3.2.2 according to this [guide][1]

- Please make sure that in your `composer.lock`, that all `allmyhomes` packages are from `composer.envs.io`, we can do that by searching in `composer.lock` for `composer.smartexpose.com` and result should be `0`

**If the result isn't `0`, you can remove and re-require this package or all allmyhomes packages as follows:**

1.

```shell script
php -d memory_limit=-1 /usr/local/bin/composer remove \
  allmyhomes/laravel-token-verification \
  allmyhomes/laravel-ddd-abstractions \
  allmyhomes/php-event-projections \
  allmyhomes/contract-mock \
  allmyhomes/contract-utils \
  allmyhomes/laravel-access-token-faker \
  allmyhomes/laravel-contract-tester \
  allmyhomes/php-codesniffer
```

2 .

```shell script
php artisan cache:clear
```

If the command doesn't run successfully delete (app.php and autoload.php) in src/Infrastructure/Boilerplate/Laravel/bootstrap/cache.

3 .

```shell script
php -d memory_limit=-1 /usr/local/bin/composer require \
  allmyhomes/laravel-token-verification \
  allmyhomes/laravel-ddd-abstractions \
  allmyhomes/php-event-projections

php -d memory_limit=-1 /usr/local/bin/composer require --dev \
  allmyhomes/contract-mock \
  allmyhomes/contract-utils \
  allmyhomes/laravel-access-token-faker \
  allmyhomes/laravel-contract-tester \
  allmyhomes/php-codesniffer
```

- Now by checking `composer.lock`, the results for `composer.smartexpose.com` should be `0`

[1]: <https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/backend/boilerplate/MICROSERVICES-MERGING-BOILERPLATE.md>
