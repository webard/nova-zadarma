<?php

namespace Webard\NovaZadarma\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class NovaZadarmaController extends Controller
{
    private $config;

    public function index(NovaRequest $request, string $resourceUriKey, $resourceId)
    {
        $config = config('nova-profile.'.$resourceUriKey);

        if ($config === null) {
            throw new \Exception('Profile page is not configured for ['.$resourceUriKey.'] resource');
        }

        $this->config = $config;

        $timeline = $this->prepareTimeline($request);

        //dump(['timeline', $timeline]);

        return inertia('NovaProfile', [
            'counters' => $this->prepareCounters($request),
            'information' => $this->prepareInformation($request),
            'timeline' => $timeline,
        ]);
    }

    private function prepareCounters(NovaRequest $request): array
    {
        return collect($this->config['counters'])->map(
            fn ($counter) => (new $counter($request))->toArray()
        )->toArray();
    }

    public function prepareInformation(NovaRequest $request): array
    {
        $class = $this->config['information'];

        return (new $class($request))->toArray();
    }

    public function prepareTimeline(NovaRequest $request): array
    {
        $class = $this->config['timeline'];

        return (new $class($request))->toArray();
    }
}
