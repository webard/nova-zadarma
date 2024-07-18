<?php

namespace Webard\NovaZadarma\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webard\NovaZadarma\Models\PhoneCall;
use Webard\NovaZadarma\Models\PhoneCallUser;

trait HasPhoneCalls
{
    public function phoneCalls(): BelongsToMany
    {
        return $this->belongsToMany(PhoneCall::class)
            ->using(PhoneCallUser::class)
            ->withPivot(['role']);
    }
}
