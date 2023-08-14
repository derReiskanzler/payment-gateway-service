<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandHandler;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Traits\ResponseFormatTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;

class EventSourcingController extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ResponseFormatTrait;
    use ValidatesRequests;

    public function __construct()
    {
    }

    /**
     * @param string        $command   Command Class
     * @param Request       $request   Request
     * @param array<string> $metadata  Metadata to be added to the command
     * @param ?CommandId    $commandId Command Id
     *
     * @throws BindingResolutionException
     */
    protected function getCommand(string $command, Request $request, array $metadata = [], ?CommandId $commandId = null): Command
    {
        return app()->make($command, [
            'payload' => $request->request->all(),
            'metadata' => $metadata,
            'commandId' => $commandId,
        ]);
    }

    /**
     * @param string $commandHandler CommandHandler Class
     *
     * @throws BindingResolutionException
     */
    protected function getCommandHandler(string $commandHandler): CommandHandler
    {
        return app()->make($commandHandler);
    }
}
