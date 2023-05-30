<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Illuminate\Support\Facades\Storage;

class DownloadController extends LfmController
{
    public function getDownload()
    {
        $file_name = request('file');
        $file = $this->lfm->setName($file_name);

        if (!Storage::disk($this->helper->config('disk'))->exists($file->path('storage'))) {
            abort(404);
        }

        $disk = Storage::disk($this->helper->config('disk'));
        $config = $disk->getConfig();

        if (key_exists('driver', $config) && $config['driver'] == 's3') {
            $duration = $this->helper->config('temporary_url_duration');
            return response()->streamDownload(
                function () {
                    echo file_get_contents($disk->temporaryUrl($file->path('storage'), now()->addMinutes($duration)));
                }, $file_name,
            );
        } else {
            return response()->download($file->path('absolute'));
        }
    }
}
