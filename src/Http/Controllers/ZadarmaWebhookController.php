<?php

declare(strict_types=1);

namespace Webard\NovaZadarma\Http\Controllers;

use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            'NOTIFY_OUT_START' => config('nova-zadarma.webhooks.classes.outgoing_call_start'),
            'NOTIFY_OUT_END' => config('nova-zadarma.webhooks.classes.outgoing_call_end'),
            'NOTIFY_START' => config('nova-zadarma.webhooks.classes.incoming_call_start'),
            'NOTIFY_END' => config('nova-zadarma.webhooks.classes.incoming_call_end'),
            'NOTIFY_RECORD' => config('nova-zadarma.webhooks.classes.phone_call_recording'),
            default => throw new NotFoundHttpException('Unsupported event'),
        };
    }
}
