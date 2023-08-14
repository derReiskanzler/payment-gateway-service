<?php

declare(strict_types=1);

namespace Tests\PHPUnit;

use Allmyhomes\AccessTokenFaker\Contracts\ApiTesterAccessTokenFakerContract;
use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\EventProjections\Services\Configurations\LaravelProjectionConfigurationProvider;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\ApiClient;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Interfaces\CrudApiClientInterface;
use Allmyhomes\LaravelKeycloakGuardPackage\Helpers\TokenGeneratorHelper;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\TestCase;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\Projection\ProjectionManager;
use Prophecy\PhpUnit\ProphecyTrait;
use Tests\PHPUnit\Extenders\InteractsWithEventStore;
use Tests\PHPUnit\Extenders\InteractsWithLaravelApplication;
use Tests\PHPUnit\Extenders\InteractsWithLaravelContainer;
use Tests\PHPUnit\Helpers\RunProjectionTrait;
use Tests\PHPUnit\Helpers\StoreTruncateSchemaFunctionTrait;

/**
 * Base test class for tests which needs Laravel and postgres database.
 */
abstract class IntegrationTestCase extends TestCase
{
    use InteractsWithEventStore;
    use InteractsWithLaravelApplication;
    use InteractsWithLaravelContainer;
    use ProphecyTrait;
    use RunProjectionTrait;
    use StoreTruncateSchemaFunctionTrait;

    /**
     * Ensures that the API client will be mocked for each test via prophecy.
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var DatabaseManager $db */
        $db = app()->get('db');
        $connection = $db->connection();
        $eventStoreConnection = $db->connection('event_store_testing');
        self::storeTruncateSchemaFunction($connection);
        self::storeTruncateSchemaFunction($eventStoreConnection);
        $connection->getPdo()->exec("SELECT truncate_schema('public');");
        $eventStoreConnection->getPdo()->exec("SELECT truncate_schema('public');");

        // force mocking of api client
        $apiClient = $this->prophesize(ApiClient::class);

        $this->app->instance(ApiClient::class, $apiClient->reveal());
        $this->app->instance(CrudApiClientInterface::class, $apiClient->reveal());
    }

    /**
     * Creates the application.
     */
    final public function createApplication(): Application
    {
        $app = require __DIR__.'/../../src/Infrastructure/Boilerplate/Laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * It runs all consuming projections one at another.
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    protected function runAllProjections(): void
    {
        $projectionConfigurationProvider = new LaravelProjectionConfigurationProvider('projections');

        $projectionNames = $projectionConfigurationProvider->getProjectionConfigurationNames(
            LaravelProjectionConfigurationProvider::CONSUMING_PROJECTION_TYPE
        );

        foreach ($projectionNames as $projectionName) {
            $projectionsConfiguration = new LaravelProjectionConfigurationProvider('projections');
            $projectionConfiguration = $projectionsConfiguration->getProjectionConfiguration(
                $projectionName,
                LaravelProjectionConfigurationProvider::CONSUMING_PROJECTION_TYPE
            );

            /** @var ProjectionManager $projectionManager */
            $projectionManager = $this->service(ProjectionManager::class);

            $eventHandler = $this->service((string) $projectionConfiguration->getHandler());

            $projector = $projectionManager->createProjection($projectionName);
            $projector
                ->fromStreams(...$projectionConfiguration->getStreamNames())
                ->whenAny(
                    static function (array $state, Message $event) use ($eventHandler): array {
                        $eventData = new EventDTO(
                            $event->uuid()->toString(),
                            $event->messageName(),
                            $event->payload(),
                            $event->metadata(),
                            $event->createdAt()
                        );
                        $eventHandler->handle($eventData);

                        return $state;
                    }
                )
                ->run(false);
        }
    }

    /**
     * Prepares the OAuth Header.
     *
     * @param array<string> $scopes scopes
     * @param array<int>    $roles  roles
     *
     * @throws BindingResolutionException
     *
     * @return string[]
     */
    protected function authHeader(array $scopes, ?string $userId = null, array $roles = []): array
    {
        /** @var ApiTesterAccessTokenFakerContract $tokenFaker */
        $tokenFaker = $this->service(ApiTesterAccessTokenFakerContract::class);

        $tokenFaker->setScopes($scopes);

        if ($userId) {
            $tokenFaker->setUser($userId, $roles);
        }

        return [
            'Authorization' => 'Bearer '.$tokenFaker->getToken(),
        ];
    }

    /**
     * Return keycloak authentication header array.
     *
     * @param string[] $payload
     *
     * @return string[]
     */
    protected function keycloakAuthHeader(array $payload = []): array
    {
        $private = config('KEYCLOAK_REALM_PRIVATE_KEY');
        $public = config('KEYCLOAK_REALM_PUBLIC_KEY');

        config(['auth.providers.users.model' => User::class]);
        config(['auth.guards.keycloak.driver' => 'AMHkeycloak']);
        config(['auth.guards.keycloak.provider' => 'users']);

        $tokenHelper = new TokenGeneratorHelper($private, $public);
        $tokenHelper->generatePayload($payload);

        return [
            'Authorization' => 'Bearer '.$tokenHelper->getToken(),
        ];
    }
}
