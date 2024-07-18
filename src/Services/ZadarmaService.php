<?php

declare(strict_types=1);

namespace Webard\NovaZadarma\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Zadarma_API\Api;

class ZadarmaService
{
    protected Api $api;

    public function __construct(protected string|null $key, protected string|null $secret)
    {
        $this->api = new Api($this->key, $this->secret);
    }

    public function getWebrtcLogin(string $sipLogin, string $sip): ?string
    {
        return $sipLogin.'-'.$sip;
    }

    // Zadarma has limit 100 requests per minute
    public function getWebrtcKey(string $sip): ?string
    {
        return $this->api->getWebrtcKey($sip)->key;
    }

    // TODO: remove from this
    public function getRecordingUrl(string $pbxCallId): ?string
    {
        $pbxRecordRequest = $this->api->getPbxRecord(null, $pbxCallId, 180);

        if (count($pbxRecordRequest->links) > 0) {
            return $pbxRecordRequest->links[0];
        }

        return $pbxRecordRequest->link;
    }
}
