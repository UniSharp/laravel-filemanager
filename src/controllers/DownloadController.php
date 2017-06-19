<?php

namespace Unisharp\Laravelfilemanager\controllers;

class DownloadController extends LfmController
{
    public function getDownload()
    {
        return response()->download($this->lfm->path('full', request('file')));
    }
}
