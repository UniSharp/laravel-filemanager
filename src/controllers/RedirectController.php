<?php

namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\LfmStorage;

class RedirectController extends LfmController
{
    public function showFile()
    {
        $storage = app(LfmStorage::class);
        $request_url = urldecode(request()->url());
        $storage_path = str_replace(url('/'), '', $request_url);

        if (! $storage->exists($storage_path)) {
            abort(404);
        }

        return response($storage->getFile($storage_path))
            ->header('Content-Type', $storage->mimeType($storage_path));
    }
}
