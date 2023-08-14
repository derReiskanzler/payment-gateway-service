# Deprecation

API Deprecation is a natural process in the API lifecycle. From time to time, the API endpoint will need to evolve or
will be no longer supported and might not exist in the future.

According to [Allmyhomes Deprecation Guidelines][1], we will need to update our route to add the needed headers.

## How to use

Our deprecation is route based so would need to inject a middleware to apply the deprecation rule.

- We need to pass the middleware `deprecate.response` to the specific endpoint.
- We need to pass the headers value

  - deprecation -> date in `Y-m-d H:i:s` format or boolean value `true`
  - link -> pass a link to service contract
  - sunset -> date in `Y-m-d H:i:s` format

- Validate that the headers are added as expected to the `Response` object
- Validate a logged warning added to your logs

```php
$api->post('payment', [
    'uses' => 'Application\v1\Payment\Controllers\PaymentController@store',
    'middleware' => 'deprecate.response',
    'deprecate.response' => [
        'deprecation' => '2020-04-01 23:00:00',
        'link' => 'https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/blob/master/Contracts/Providing/api-unparsed.yaml',
        'sunset' => '2020-05-01 23:00:00',
    ],
]);

$api->put('payment', [
    'uses' => 'Application\v1\Payment\Controllers\PaymentController@store',
    'middleware' => 'deprecate.response',
    'deprecate.response' => [
        'deprecation' => true,
        'link' => 'https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate/-/blob/master/Contracts/Providing/api-unparsed.yaml',
        'sunset' => '2020-05-01 23:00:00',
    ],
]);
```

[1]: <https://gitlab.smartexpose.com/allmyhomes/technology/-/blob/master/backend/api-guidelines/Deprecation.md>
