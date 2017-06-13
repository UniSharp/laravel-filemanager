<?php

namespace Unisharp\Laravelfilemanager\controllers;

class RedirectController extends LfmController
{
    public function showFile()
    {
        $request_url = urldecode(request()->url());
        $storage_path = str_replace(url('/'), '', $request_url);
        $full_path = $this->disk_root . $storage_path;

        if (!parent::exists($full_path)) {
            abort(404);
        }

        return response(parent::getFile($storage_path))
            ->header("Content-Type", parent::getFileType($full_path));
    }
}
