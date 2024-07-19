<?php

declare(strict_types=1);

namespace Webard\NovaZadarma\Http\Controllers;

use Illuminate\Http\Request;
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

class ZadarmaWebhookForwardController
{
    private function fixRequestPhoneNumbers(Request $request): void
    {
        // for some reason, caller_id or called_did sometimes does not have "+" at the beginning
        // trying to fix this to make it work with validator
        $calledDid = $request->input('called_did');
        if ($calledDid !== null && str_contains($calledDid, '+') === false) {
            $request->merge(['called_did' => '+'.$calledDid]);
        }

        $callerId = $request->input('caller_id');
        if ($callerId !== null && str_contains($callerId, '+') === false) {
            $request->merge(['caller_id' => '+'.$callerId]);
        }
    }

    public function __invoke(WebhookRequest $request): Response
    {
        $this->fixRequestPhoneNumbers($request);

        $controller = $this->matchController($request->input('event'));

        return $this->forwardRequestToController($controller);
    }

    private function forwardRequestToController(string $controller)
    {
        $container = app();

        /** @var Route $router */
        $router = $container->make(Route::class);

        $router->name('::'.$controller);

        $controllerInstance = $container->make($controller);

        return (new ControllerDispatcher($container))->dispatch($router, $controllerInstance, '__invoke');
    }

    private function matchController(string $event)
    {
        return match ($event) {
            'NOTIFY_OUT_START' => OutgoingCallStartController::class,
            'NOTIFY_OUT_END' => OutgoingCallEndController::class,
            'NOTIFY_START' => IncomingCallStartController::class,
            'NOTIFY_END' => IncomingCallEndController::class,
            'NOTIFY_RECORD' => RecordingController::class,
            default => throw new NotFoundHttpException('Unknown event'),
        };
    }
}
