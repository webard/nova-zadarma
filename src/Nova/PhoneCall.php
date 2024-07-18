<?php

namespace Webard\NovaZadarma\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Enums\PhoneCallType;
use Webard\NovaZadarma\Models\PhoneCall as ModelsPhoneCall;
use Webard\NovaZadarma\Nova\Fields\EnumBadge;

class PhoneCall extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = ModelsPhoneCall::class;

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
                ->sortable()
                ->filterable(),

            BelongsTo::make(Nova::__('Caller'), 'caller', config('nova-zadarma.resources.user'))
                ->sortable()
                ->filterable()
                ->searchable(),

            BelongsTo::make(Nova::__('Receiver'), 'receiver', config('nova-zadarma.resources.user'))
                ->sortable()
                ->filterable()
                ->searchable(),

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

            Number::make(Nova::__('Duration'), 'duration')
                ->sortable()
                ->filterable()
                ->nullable(),

            DateTime::make(Nova::__('Created At'), 'created_at')
                ->sortable()
                ->filterable(),

            DateTime::make(Nova::__('Ended At'), 'ended_at')
                ->sortable()
                ->filterable()
                ->nullable(),
        ];
    }
}
