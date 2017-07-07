<?php

namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\Storage;

class RedirectController extends LfmController
{
    public function showFile()
    {
        $request_url = urldecode(request()->url());
        $storage_path = str_replace(url('/'), '', $request_url);

        if (! Storage::exists($storage_path)) {
            abort(404);
        }

        return response(Storage::get($storage_path))
            ->header('Content-Type', Storage::mimeType($storage_path));
    }
}
