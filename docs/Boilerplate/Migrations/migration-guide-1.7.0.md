# v1.7.0 Migration Guide

This document outlines the changes applied in Boilerplate **v1.7.0** from **v1.6.x**.

Please note that there are changes marked as "breaking", we listed those changes from that are likely to appear in our backend-services but if you encounter any other issues then It's recommend to look into full migration guide of:

- Laravel: <https://laravel.com/docs/5.8/upgrade#upgrade-5.8.0>
- Carbon: <https://carbon.nesbot.com/docs/#api-carbon-2>

To upgrade to v1.7.0, please follow this [guide](./boilerplate-migration.md)

## Added

### Codeception v3.x

Use of official `Codeception v3.x` instead of outdated `allmyhomes/codeception`. [More details](https://github.com/Codeception/Codeception)

## Changed

### Upgrade to Carbon 2.x

Upgrading our boilerplate carbon package to `v2.x` from `v1.x`. [More details](https://carbon.nesbot.com/docs/#api-carbon-2)

### Upgrade to Laravel 5.8.x

Upgrading our boilerplate Laravel to `v5.8.x` from `v5.7.x`. [More details](https://laravel.com/docs/5.8/upgrade#upgrade-5.8.0)

### Migrations & bigIncrements

As of Laravel 5.8, migration stubs use the bigIncrements method on ID columns by default. Previously, ID columns were created using the increments method. [More details](https://laravel.com/docs/5.8/upgrade#sqlite)

### Eloquent - Model Names Ending With Irregular Plurals

As of Laravel 5.8, multi-word model names ending in a word with an irregular plural are now correctly pluralized. [More details](https://laravel.com/docs/5.8/upgrade#model-names-ending-with-irregular-plurals)

### Environment Variable Parsing

The phpdotenv package that is used to parse .env files has released a new major version, which may impact the results returned from the env helper. Specifically, the # character in an unquoted value will now be considered a comment instead of part of the value. [More details](https://laravel.com/docs/5.8/upgrade#environment-variable-parsing)

### Email Validation

The email validation rule now checks if the email is [RFC6530](https://tools.ietf.org/html/rfc6530) compliant, making the validation logic consistent with the logic used by SwiftMailer. In Laravel 5.7, the email rule only verified that the email was [RFC822](https://tools.ietf.org/html/rfc822) compliant.

### Read-Only env Helper

Previously, the env helper could retrieve values from environment variables which were changed at runtime. In Laravel 5.8, the env helper treats environment variables as immutable. If you would like to change an environment variable at runtime, consider using a configuration value that can be retrieved using the config helper:

Previous behavior:

```php
dump(env('APP_ENV')); // local
putenv('APP_ENV=staging');
dump(env('APP_ENV')); // staging
 ```

New behavior:

```php
dump(env('APP_ENV')); // local
putenv('APP_ENV=staging');
dump(env('APP_ENV')); // local
 ```

### The TransformsRequest Middleware

The transform method of the `Illuminate\Foundation\Http\Middleware\TransformsRequest` middleware now receives the "fully-qualified" request input key when the input is an array:

```php
'employee' => [
    'name' => 'Taylor Otwell',
],

/**
 * Transform the given value.
 * @param  string  $key
 * @param  mixed  $value
 * @return mixed
*/
protected function transform($key, $value) {
   dump($key); // 'employee.name' (Laravel 5.8) dump($key); // 'name' (Laravel 5.7)
}
```

### Prefer String And Array Classes Over Helpers

All `array_*` and `str_*` global helpers have been deprecated. You should use the  `Illuminate\Support\Arr` and `Illuminate\Support\Str` methods directly.

The [...] helpers have been moved to the new `laravel/helpers package` which offers a backwards compatibility layer for all of the global array and string functions.

### Testing: setUp & tearDown Methods

The setUp and tearDown methods now require a void return type:

```php
protected function setUp(): void;
protected function tearDown(): void;
```

## Removed

### Allmyhomes/codeception

Removed `allmyhomes/codeception` outdated package and uses the official `Codeception` library instead
