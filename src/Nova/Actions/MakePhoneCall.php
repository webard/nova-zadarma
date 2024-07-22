<?php

namespace Webard\NovaZadarma\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Nova;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Enums\PhoneCallType;
use Webard\NovaZadarma\Enums\PhoneCallUserRole;
use Webard\NovaZadarma\Events\OutgoingPhoneCall\OutgoingPhoneCallCreated;
use Webard\NovaZadarma\Models\PhoneCall;

class MakePhoneCall extends Action
{
    // Phone Call can be made only to one user at time
    public $sole = true;

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $receiver = $models[0];

        $caller = auth()->user();

        if ($caller === null) {
            return ActionResponse::danger(Nova::__('User not found'));
        }

        $phoneCall = new PhoneCall([
            'type' => PhoneCallType::Outgoing,
            'caller_sip' => $caller->zadarma_sip,
            'receiver_phone_number' => $receiver->phone_number,
            'disposition' => PhoneCallDisposition::Pending,
            'started_at' => now(config('app.timezone')),
        ]);

        $phoneCall->caller()->associate($caller);
        $phoneCall->receiver()->associate($receiver);

        $phoneCall->save();

        $phoneCall->users()->attach([
            $caller->id => ['role' => PhoneCallUserRole::Caller],
            $receiver->id => ['role' => PhoneCallUserRole::Receiver],
        ]);

        event(new OutgoingPhoneCallCreated($phoneCall));

        return ActionResponse::modal('InitZadarmaCall',
            [
                'id' => $phoneCall->id,
                'resource_url' => '/resources/users/'.$receiver->id,
                'phone' => $receiver->phone_number,
                'title' => $receiver->name,
            ]
        );
    }
}
