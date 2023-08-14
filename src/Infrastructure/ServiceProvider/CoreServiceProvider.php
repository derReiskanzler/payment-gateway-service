<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\ServiceProvider;

use Allmyhomes\Application\UseCase\PopulateProspect\Repository\ProspectRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Infrastructure\Outbound\Repository\Persistence\Prospect\ProspectRepository;
use Allmyhomes\Infrastructure\Outbound\Repository\Persistence\Reservation\ReservationRepository;
use Allmyhomes\Infrastructure\Outbound\Repository\Persistence\Unit\UnitRepository;
use Illuminate\Support\ServiceProvider;

final class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
         * Repositories
         */
        $this->app->bind(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
        $this->app->bind(ProspectRepositoryInterface::class, ProspectRepository::class);
    }
}
