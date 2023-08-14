# External microservice communication

To communicate with other microservices, we use the _OAuth2HttpClient_ class which is
a custom wrapper of the GuzzleHttp\Client with built-in OAuth2 authorization.

_OAuth2HttpClient_ requests an Access Token / Refresh Token from the Auth Service.
The wrapper utilizes the `OAuth2Middleware` from `kamermans/guzzle-oauth2-subscriber`.

_OAuth2HttpClient_ is located in _Infrastructure/Util/Auth/OAuth2HttpClient.php_

To obtain parsed JSON responses and maintain consistency between our Controller HTTP
methods and external HTTP methods, an _ApiClient_ helper class and supporting Interfaces
are provided.

_ApiClient_ is a very light wrapper for all standard CRUD and REST
methods used in our boilerplate. However, you do _not_ have to use this class if
you prefer to call a service directly via the _OAuth2HttpClient_.

To call external services, follow these steps:

- In the .env and _src/Infrastructure/Boilerplate/Laravel/config/amhclient.php_, add/update the external service configuration, e.g.:

```text
#########################
# AMH OAuth credentials #
#########################

APP_AMH_OAUTH_CLIENT_ID=
APP_AMH_OAUTH_CLIENT_SECRET=
APP_AMH_OAUTH_GRANT_TYPE=client_credentials
APP_AMH_OAUTH_USERNAME=
APP_AMH_OAUTH_PASSWORD=

##################################
# External service configuration #
##################################

#AUTH_BASE_URL=http://mock-server/auth
#AVAILABILITY_SEARCH_BASE_URL=http://mock-server/availability-search
#BROCKERAGE_MANAGEMENT_BASE_URL=http://mock-server/brokerage-management
#BUYER_BASE_URL=http://mock-server/buyer
#BUYER_SEARCH_BASE_URL=http://mock-server/buyer-search
#FUNNEL_MANAGEMENT_BASE_URL=http://mock-server/funnel-management
#LEAD_BASE_URL=http://mock-server/lead
#MAIL_BASE_URL=http://mock-server/mail
#PROJECT_INFORMATION_BASE_URL=http://mock-server/project-information
#PROJECT_SETTING_BASE_URL=http://mock-server/project-setting
#REALTIME_BASE_URL=http://mock-server/rts
#TASK_MANAGEMENT_BASE_URL=http://mock-server/task-management
#USER_BASE_URL=http://mock-server/user
```

```php
<?php

return [
    'amh_oauth_credentials' => [
        'client_id' => env('APP_AMH_OAUTH_CLIENT_ID'),
        'client_secret' => env('APP_AMH_OAUTH_CLIENT_SECRET'),
        'grant_type' => env('APP_AMH_OAUTH_GRANT_TYPE', 'client_credentials'),
        'username' => env('APP_AMH_OAUTH_USERNAME', ''),
        'password' => env('APP_AMH_OAUTH_PASSWORD', ''),
    ],
    'service' => [
        'auth' => [
            'base_url' => env('AUTH_BASE_URL', 'http://auth'),
        ],
        'availability-search' => [
            'base_url' => env('AVAILABILITY_SEARCH_BASE_URL', 'http://availability-search'),
        ],
        'brokerage-management' => [
            'base_url' => env('BROCKERAGE_MANAGEMENT_BASE_URL', 'http://brokerage-management'),
        ],
        'buyer' => [
            'base_url' => env('BUYER_BASE_URL', 'http://buyer'),
        ],
        'buyer-search' => [
            'base_url' => env('BUYER_SEARCH_BASE_URL', 'http://buyer-search'),
        ],
        'funnel-management' => [
            'base_url' => env('FUNNEL_MANAGEMENT_BASE_URL', 'http://funnel-management'),
        ],
        'lead' => [
            'base_url' => env('LEAD_BASE_URL', 'http://lead'),
        ],
        'mail' => [
            'base_url' => env('MAIL_BASE_URL', 'http://mail'),
        ],
        'project-information' => [
            'base_url' => env('PROJECT_INFORMATION_BASE_URL', 'http://project-information'),
        ],
        'project-setting' => [
            'base_url' => env('PROJECT_SETTING_BASE_URL', 'http://project-setting'),
        ],
        'realtime' => [
            'base_url' => env('REALTIME_BASE_URL', 'http://realtime'),
        ],
        'task-management' => [
            'base_url' => env('TASK_MANAGEMENT_BASE_URL', 'http://task-management'),
        ],
        'user' => [
            'base_url' => env('USER_BASE_URL', 'http://user'),
        ],
    ],
];
```

- To call an external AMH microservice in your Domain Service:

Import _ApiClient_:

```php
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\ApiClient;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Traits\GetServiceConfigTrait;
```

Add the following at the very beginning of the Service class declaration:

```php
/**
 * @var ApiClient
 */
private $amhClient;

/**
 * @var array
 */
protected $userUrls;

/**
 * ProjectFunnelService constructor.
 * @param ProjectFunnelRepository $repository ProjectFunnelRepository
 */
public function __construct(ProjectFunnelRepository $repository, ApiClient $apiClient)
{
    parent::__construct($repository);
    $this->amhClient = $apiClient;
    $this->userUrls = $this->amhClient->getServiceConfig('user');
}
```

Now you can use the _ApiClient_ anywhere in the Service as follows:

Example 1: call the User service

```php
$response = $this->amhClient->get($this->userUrls['base_url'] . '/v1/users');
$response = $this->amhClient->findById($this->userUrls['base_url'] . '/v1/users', '3a3e46bd-4afe-3d8d-9396-63b3fd6ed1a8');
```

Example 2: call the User service using request body

```php
$data = [
    'email' => 'user@allmyhomes.com',
    'password' => 'Secret123!',
    'roles' => [1,2,3]
];
$this->amhClient->post($this->userUrls['base_url'] . '/v1/users', [
        'form_params' => $data
]);
        ```

*NOTICE*: In case your request fails for some reasons, try to use `json` (or `\GuzzleHttp\RequestOptions::JSON` constant) instead of `form_params`, because there may be an issue with accepted `content-type` by external service(s). A good example will be `Mail service`: the external service which we use for sending emails, expects to get the json decoded data, that's why we should send all request to Mail service using a `json` parameter instead of `form_params`.

You can read more about Guzzle request options [here](http://docs.guzzlephp.org/en/stable/request-options.html). We also recomend you to use `GuzzleHttp\RequestOptions::FORM_PARAMS` constant instead of a simple string `form_params`.

Example 3: call a non-AMH public service

```php
/**
 * Calls JsonPlaceholder
 *
 * @throws \Exception
 * @return mixed
 */
public function getPostsFromFakeAPI()
{
    $this->amhClient->withAuth = false;

    return $this->amhClient->get('https://jsonplaceholder.typicode.com/posts');
}
```

Then invoke the ApiClient instance (declared and instatiated in the service) in
the Controller that imports it, e.g.,

```php
public function index(UserRequest $request)
{
    return $this->applicationService->getPostsFromFakeAPI();
    / ... /
}
```