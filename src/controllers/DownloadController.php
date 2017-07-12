<?php

namespace UniSharp\LaravelFilemanager\controllers;

class DownloadController extends LfmController
{
    public function getDownload()
    {
        return response()->download($this->lfm->setName(request('file'))->path('absolute'));
    }
}
