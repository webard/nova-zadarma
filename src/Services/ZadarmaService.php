<?php

declare(strict_types=1);

namespace Webard\NovaZadarma\Services;

use Illuminate\Support\Facades\Cache;
use Zadarma_API\Api;

class ZadarmaService
{
    protected Api $api;

    public function __construct()
    {
        $this->api = new Api(config('nova-zadarma.auth.key'), config('nova-zadarma.auth.secret'));
    }

    public function getWebrtcLogin(string $sip): string
    {
        return config('nova-zadarma.auth.login_suffix', 'UNKNOWN').'-'.$sip;
    }

    public function getWebrtcKey(string $sip): ?string
    {
        // Must be cached due to Zadarma API limits
        return Cache::remember('nova-zadarma-key2-'.$sip, 60, function () use ($sip) {
            return $this->api->getWebrtcKey($sip)->key;
        });
    }

    public function getRecordingUrl(string $pbxCallId): ?string
    {
        $pbxRecordRequest = $this->api->getPbxRecord(null, $pbxCallId, 180);

        if (count($pbxRecordRequest->links) > 0) {
            return $pbxRecordRequest->links[0];
        }

        return $pbxRecordRequest->link;
    }
}
