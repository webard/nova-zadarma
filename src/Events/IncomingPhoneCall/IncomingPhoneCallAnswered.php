<?php

namespace Webard\NovaZadarma\Events\IncomingPhoneCall;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Webard\NovaZadarma\Models\PhoneCall;

class IncomingPhoneCallAnswered
{
    use Dispatchable;
    use SerializesModels;

    public PhoneCall $phoneCall;

    /**
     * Create a new event instance.
     */
    public function __construct(
        PhoneCall $phoneCall
    ) {
        $this->phoneCall = $phoneCall;
    }
}
