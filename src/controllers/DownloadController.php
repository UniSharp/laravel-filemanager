<?php

namespace Unisharp\Laravelfilemanager\controllers;

class DownloadController extends LfmController
{
    public function getDownload()
    {
        return response()->download(parent::getCurrentPath(request('file')));
    }
}
