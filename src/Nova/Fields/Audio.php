<?php

declare(strict_types=1);

namespace Webard\NovaZadarma\Nova\Fields;

use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\PresentsAudio;

/**
 * Audio Nova field using temporaryUrl to load audio from storage.
 * Useful for non-public buckets.
 */
class Audio extends File
{
    use PresentsAudio;

    const PRELOAD_AUTO = 'auto';

    const PRELOAD_METADATA = 'metadata';

    const PRELOAD_NONE = 'none';

    public $showOnIndex = true;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'audio-field';

    /**
     * The file types accepted by the field.
     *
     * @var string
     */
    public $acceptedTypes = 'audio/*';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  string|null  $disk
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):mixed)|null  $storageCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $disk = 'public', $storageCallback = null)
    {
        parent::__construct($name, $attribute, $disk, $storageCallback);

        $this->preview(function ($value) {

            if ($value === null) {
                return null;
            }

            if (str_starts_with($value, 'http')) {
                return $value;
            }

            $disk = $this->getStorageDisk();

            $visibility = config("filesystems.disks.{$disk}.visibility", 'private');

            if ($visibility === 'public') {
                return Storage::disk($disk)
                    ->url($value);
            } elseif (method_exists(Storage::disk($disk), 'temporaryUrl')) {
                return Storage::disk($disk)
                    ->temporaryUrl($value, now()->addSeconds(config('nova-zadarma.recordings.private_disk_ttl', 600)));
            } else {
                return null;
            }
        });
    }
}
