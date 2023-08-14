# Testing with faked JWT-Tokens in the laravel-api-boilerplate

## Context

If you want to write proper ApiTests against an OAuth-protected endpoint you have a small problem:
Because you most likely don't have the private key commited in the repository that is matching your public key, your test will fail as the signature of your tokens is not considered valid.

A while back we implemented a little workaround: storing an additional pair of keys and switch them in if in the testing environment. The solution had worked, but it was kinda ugly.

Fortunately we have now properly implemented a way to solve this:
We integrated a laravel-access-token-faker into the ApiTester of the Boilerplate!

Each time you generate an Instance of the ApiTester (e.g. everytime you run an api-test in Codeception), the keys used for jwt are swapped temporarly with a fresh generated matching pair. That way, the signature matches again.

## General

The `Allmyhomes\AccessTokenFaker\Services\AccessTokenFakerService` provides testing-framework-agnostic functionalities.

Simply create a new instance of the `AccessTokenFakerService` *(no constructor params)* yourself and use following methods as needed:

### `public function fakeKeys(): void;`

- Fakes the keys
- **You should call this method almost always after creating the Service, or it won't work**

### `public function setScopes(array $scopes): void`

- Sets/Overrides the current scopes

### `public function addScope(string $scope): void`

- Adds a scope to the current scopes

### `public function setUser(string $id, array $roles = []): void`

- Sets the user
- optionally with roles

### `public function attachUserRole(int $role): void`

- Attaches a role to the current user

### `public function setCustomClaim(string $key, $value): void`

- Sets a custom claim

### `public function getToken(): string`

- Generates the token
- Make sure you have called `fakeKeys()` before to ensure that the signed token will be accepted

### `public function clearScopes(): void`

- Clears all scopes

### `public function clearUser(): void`

- Clears all user data

### `public function clearClaims(): void`

- Clears all claims

### `public function resetClaims(): void`

- Clear all claims and set defaults

### `public function clear(): void`

- resets the service completely
- **with exception of the faked keys**

## Codeception

We have a dedicated implementation of the `AccessTokenFakerService` for the ApiTester in Codeception for your convinience

### How to use with Codeception ApiTester

(1) Adjust your payload data with the methods listed below (e.g. `$I->setScopes()` & `$I->setUserObject()`)

(2) call `$I->setAuthorizationHeader()`. The token is now set for all subsequent requests.

(3) do your request, e.g. `$I->sendPost()`.

### Overview of methods on the Codeception ApiTester for the Token-Faker

We also added some methods to interact with the payload of your token, so you can easily add scopes, user-data or even custom claims.

#### `$I->setScopes(array $scopes): void`

- sets the scopes to $scopes
- overwrites existing scopes

#### `$I->addScope(string $scope): void`

- adds $scope to the scopes
- does not overwriting existing scopes

#### `$I->clearScopes(): void`

- removes all scopes from the resulting token

#### `$I->setUserObject(string $id, array $roles = []): void`

- sets the user-id and roles of the user
- overwrites existing user

#### `$I->attachUserRole(int $role): void`

- attaches a role to an existent user
- does not overwriting existing roles
- throws an exception if no user was set previously

#### `$I->clearUser(): void`

- removes the user from the resulting token

#### `$I->setCustomClaim(string $key, $value): void`

- sets a custom claim on the token
- overwrites claims stored on the same key
- does not overwrite other claims
- throws an exception if one tries to set a user/scope manually

#### `$I->resetClaims(): void`

- removes all technically not needed claims
- sets the default claims given in the access-token-faker-config

#### `$I->setAuthorizationHeader(bool $setContentTypeJson = true)`

- sets an authorization-header for subsequent requests with the generated bearer token
- sets the content-type to json by default, pass `false` as first parameter to skip this
