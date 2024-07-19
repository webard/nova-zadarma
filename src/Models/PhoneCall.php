<?php

namespace Webard\NovaZadarma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Enums\PhoneCallType;

class PhoneCall extends Model
{
    protected $casts = [
        'type' => PhoneCallType::class,
        'disposition' => PhoneCallDisposition::class,
        'ended_at' => 'datetime',
        'started_at' => 'datetime',
        'receiver_phone_number' => E164PhoneNumberCast::class,
        'caller_phone_number' => E164PhoneNumberCast::class,
        'is_answered' => 'boolean',
    ];

    public $fillable = [
        'pbx_call_id',
        'caller_sip',
        'receiver_sip',
        'caller_phone_number',
        'receiver_phone_number',
        'type',
        'disposition',
        'is_answered',
        'duration',
        'recording',
        'recording_disk',
        'started_at',
        'ended_at',
    ];

    public function scopePbxCallId($query, string $pbxCallId): void
    {
        $query->where('pbx_call_id', $pbxCallId);
    }

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
