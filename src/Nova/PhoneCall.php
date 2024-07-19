<?php

namespace Webard\NovaZadarma\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Enums\PhoneCallType;
use Webard\NovaZadarma\Models\PhoneCall as ModelsPhoneCall;
use Webard\NovaZadarma\Nova\Fields\Audio;
use Webard\NovaZadarma\Nova\Fields\EnumBadge;

class PhoneCall extends Resource
{
    public static $model = ModelsPhoneCall::class;

    public static $search = [
        'id',
        'caller_phone_number',
        'caller_sip',
        'receiver_phone_number',
        'receiver_sip',
    ];

    public function title()
    {
        return Nova::__(':caller to :receiver', [
            'caller' => $this->caller?->{config('nova-zadarma.models.user.name_field')} ?? $this->caller_phone_number,
            'receiver' => $this->receiver?->{config('nova-zadarma.models.user.name_field')} ?? $this->receiver_phone_number,
        ]);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make(),

            EnumBadge::make(Nova::__('Type'), 'type')
                ->enum(PhoneCallType::class)
                ->sortable()
                ->filterable(),

            EnumBadge::make(Nova::__('Disposition'), 'disposition')
                ->enum(PhoneCallDisposition::class)
                ->hideFromIndex()
                ->filterable(),

            Boolean::make(Nova::__('Is Answered'), 'is_answered')
                ->sortable()
                ->filterable(),

            Stack::make(Nova::__('Caller'), 'caller_id', [
                BelongsTo::make(Nova::__('Caller'), 'caller', config('nova-zadarma.nova_resources.user.class'))
                    ->filterable()
                    ->searchable(),

                Line::make(Nova::__('Caller Phone Number'), 'caller_phone_number')
                    ->asSubTitle(),

                Line::make(Nova::__('Caller SIP'), 'caller_sip')
                ->displayUsing(function ($value) {
                    return $value !== null ? Nova::__('SIP').': '.$value : null;
                })
                ->asSmall(),
            ])
            ->onlyOnIndex()
            ->sortable(),

            Panel::make(Nova::__('Caller'), [
                BelongsTo::make(Nova::__('Caller'), 'caller', config('nova-zadarma.nova_resources.user.class'))
                    ->hideFromIndex(),

                Text::make(Nova::__('Caller Phone Number'), 'caller_phone_number')
                    ->hideFromIndex(),

                Text::make(Nova::__('Caller SIP'), 'caller_sip')
                    ->hideFromIndex(),
            ]),

            Stack::make(Nova::__('Receiver'), 'receiver_id', [
                BelongsTo::make(Nova::__('Receiver'), 'receiver', config('nova-zadarma.nova_resources.user.class'))
                    ->filterable()
                    ->searchable(),

                Line::make(Nova::__('Receiver Phone Number'), 'receiver_phone_number')
                    ->asSubTitle(),

                Line::make(Nova::__('Receiver SIP'), 'receiver_sip')
                ->displayUsing(function ($value) {
                    return $value !== null ? Nova::__('SIP').': '.$value : null;
                })
                ->asSmall(),
            ])
            ->onlyOnIndex()
            ->sortable(),

            Panel::make(Nova::__('Receiver'), [
                BelongsTo::make(Nova::__('Receiver'), 'receiver', config('nova-zadarma.nova_resources.user.class'))
                    ->hideFromIndex(),

                Text::make(Nova::__('Receiver Phone Number'), 'receiver_phone_number')
                    ->hideFromIndex(),

                Text::make(Nova::__('Receiver SIP'), 'receiver_sip')
                    ->hideFromIndex(),
            ]),

            // BelongsToMany::make('Users', 'users', config('nova-zadarma.resources.user'))
            //     ->fields(function () {
            //         return [
            //             Select::make('Type')
            //                 ->options([
            //                     'caller' => 'Caller',
            //                     'receiver' => 'Receiver',
            //                 ])
            //                 ->displayUsingLabels(),

            //         ];
            //     }),

            Audio::make('Recording', 'recording', config('nova-zadarma.recordings.disk'))
                ->hideFromIndex(),

            Number::make(Nova::__('Duration'), 'duration')
                ->displayUsing(fn ($value) => ! empty($value) && (int) $value > 0 ? gmdate("i\m s\s", $value) : null)
                ->sortable()
                ->filterable()
                ->nullable(),

            DateTime::make(Nova::__('Started At'), 'started_at')
                ->sortable()
                ->filterable(),

            DateTime::make(Nova::__('Ended At'), 'ended_at')
                ->sortable()
                ->filterable()
                ->nullable(),
        ];
    }
}
