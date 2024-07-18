<?php

namespace Webard\NovaZadarma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User;
use Webard\NovaZadarma\Enums\PhoneCallType;

class PhoneCall extends Model
{
    protected $casts = [
        'type' => PhoneCallType::class,
        'ended_at' => 'datetime',
    ];

    public $fillable = [
        'caller_phone_number',
        'receiver_phone_number',
        'type',
        'disposition',
    ];

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(PhoneCallUser::class)
            ->withPivot(['role']);
    }
}
