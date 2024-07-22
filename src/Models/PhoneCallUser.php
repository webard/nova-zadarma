<?php

namespace Webard\NovaZadarma\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Webard\NovaZadarma\Enums\PhoneCallUserRole;

class PhoneCallUser extends Pivot
{
    protected $casts = [
        'role' => PhoneCallUserRole::class,
    ];

    public $fillable = [
        'role',
    ];

    public function getTable()
    {
        return 'phone_call_user';
    }
}
