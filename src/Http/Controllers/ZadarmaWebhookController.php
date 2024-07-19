<?php

declare(strict_types=1);

namespace Webard\NovaZadarma\Http\Controllers;

use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webard\NovaZadarma\Http\Controllers\Webhooks\IncomingCallEndController;
use Webard\NovaZadarma\Http\Controllers\Webhooks\IncomingCallStartController;
use Webard\NovaZadarma\Http\Controllers\Webhooks\OutgoingCallEndController;
use Webard\NovaZadarma\Http\Controllers\Webhooks\OutgoingCallStartController;
use Webard\NovaZadarma\Http\Controllers\Webhooks\RecordingController;
use Webard\NovaZadarma\Http\Requests\WebhookRequest;

class ZadarmaWebhookController
{
    public function __invoke(WebhookRequest $request): Response
    {
        $controller = $this->matchControllerToEvent($request->input('event'));

        return $this->forwardRequestToNextController($controller);
    }

    private function forwardRequestToNextController(string $controller)
    {
        $container = app();

        /** @var Route $router */
        $router = $container->make(Route::class);

        $router->name('::'.$controller);

        $controllerInstance = $container->make($controller);

        return (new ControllerDispatcher($container))->dispatch($router, $controllerInstance, '__invoke');
    }

    private function matchControllerToEvent(string $event)
    {
        return match ($event) {
            'NOTIFY_OUT_START' => OutgoingCallStartController::class,
            'NOTIFY_OUT_END' => OutgoingCallEndController::class,
            'NOTIFY_START' => IncomingCallStartController::class,
            'NOTIFY_END' => IncomingCallEndController::class,
            'NOTIFY_RECORD' => RecordingController::class,
            default => throw new NotFoundHttpException('Unsupported event'),
        };
    }
}
