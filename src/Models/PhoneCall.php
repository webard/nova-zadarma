<?php

namespace Webard\NovaZadarma\Models;

use Illuminate\Database\Eloquent\Model;
use Webard\NovaZadarma\Models\PhoneCallUser;

class PhoneCall extends Model
{
    public function caller() {
        $this->hasOneThrough(config('nova-zadarma.models.user'), PhoneCallUser::class, 'user_id', 'id', 'id', 'phone_call_id')->where('type', 'caller');
    }
}
