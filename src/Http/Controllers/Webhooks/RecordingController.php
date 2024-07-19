<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webard\NovaZadarma\Http\Requests\RecordingSignedRequest;
use Webard\NovaZadarma\Models\PhoneCall;
use Webard\NovaZadarma\Services\ZadarmaService;

class RecordingController
{
    public function __invoke(RecordingSignedRequest $request, ZadarmaService $zadarmaService)
    {
        $data = $request->validated();

        $phoneCall = PhoneCall::query()->pbxCallId($data['pbx_call_id'])->firstOrFail();

        $recordingUrl = $zadarmaService->getRecordingUrl($data['pbx_call_id']);

        if ($recordingUrl === null) {
            return response()->json([
                'success' => false,
                'message' => 'Recording not found',
            ]);
        }

        if (config('nova-zadarma.recordings.store', false) === true) {
            $recordingFile = file_get_contents($recordingUrl);

            $randomString = Str::random(20);

            $fileName = 'phone_call_'.$phoneCall->id.'_'.$randomString.'.mp3';

            $recording = $this->getRecordingPath().$fileName;

            Storage::disk(config('nova-zadarma.recordings.disk'))->put($recording, $recordingFile);
        } else {
            $recording = $recordingUrl;
        }

        $phoneCall->update([
            'recording' => $recording,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recording saved',
        ]);
    }

    private function getRecordingPath(): string
    {
        $path = config('nova-zadarma.recordings.path', null);

        if ($path === null) {
            return '';
        }

        if (str_ends_with($path, '/')) {
            return $path;
        } else {
            return $path.'/';
        }
    }
}
