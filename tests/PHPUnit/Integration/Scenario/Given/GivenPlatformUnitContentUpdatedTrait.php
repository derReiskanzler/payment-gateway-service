<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Prooph\Messaging\GenericDomainMessage;
use Allmyhomes\Infrastructure\Stream;
use ArrayIterator;
use DateTimeImmutable;
use EventEngine\Messaging\GenericEvent;
use Faker\Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Prooph\Common\Messaging\DomainMessage;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;

trait GivenPlatformUnitContentUpdatedTrait
{
    /**
     * @throws BindingResolutionException
     */
    final protected function givenPlatformUnitContentUpdated(
        int $unitId,
        string $unitName,
        string $languageCode = 'de-DE'
    ): void {
        /**
         * @var GenericEvent $event
         */
        $event = $this->platformUnitContentUpdatedMessage($unitId, $unitName, $languageCode);
        /** @var EventStore $eventStore */
        $eventStore = $this->service('SharedEventStore');
        $eventStore->appendTo(
            new StreamName(Stream::PROJECT_INFORMATION_SELLABLE_PROJECT_UNIT_CONTENTS_STREAM),
            new ArrayIterator([$event])
        );
    }

    abstract protected function service(string $class): mixed;

    abstract protected function faker(): Generator;

    private function platformUnitContentUpdatedMessage(
        int $unitId,
        string $unitName,
        string $languageCode
    ): DomainMessage {
        return GenericDomainMessage::fromArray([
            'uuid' => $this->faker()->uuid,
            'message_name' => 'ProjectInformation.PlatformUnitContentPublished',
            'payload' => [
                'id' => $this->faker()->uuid,
                'project_id' => $this->faker()->numberBetween(10000, 99999),
                'unit_id' => $unitId,
                'language_code' => $languageCode,
                'area_unit' => 'm²',
                'name' => $unitName,
                'building' => null,
                'floor' => null,
                'rooms' => 2.5,
                'bedrooms' => null,
                'size' => [
                    'value' => 40.55,
                    'measure' => 'm²',
                ],
                'price_per_area_unit' => null,
                'price' => [
                    'value' => 17500,
                    'currency' => 'EUR',
                ],
                'unit_type' => 'DUPLEX',
                'allowed_to_show_price_to_prospect_on_landing_page' => true,
                'documents' => [
                    'items' => [
                        'id' => 65489,
                        'type' => 'FLOOR_PLAN',
                        'url_path' => 'https:/PathToDocument',
                    ],
                ],
                'occurred_at' => '2020-06-27T21:37:45.531877',
            ],
            'metadata' => [
                '_aggregate_id' => $unitId,
                '_aggregate_type' => 'SellableUnitContent',
                '_aggregate_version' => $this->nextAggregateVersion('SellableUnitContent', (string) $unitId),
            ],
            'created_at' => new DateTimeImmutable(),
        ]);
    }
}
