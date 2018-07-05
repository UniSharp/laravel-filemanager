<?php

namespace UniSharp\LaravelFilemanager\Controllers;

/**
 * Class DownloadController.
 */
class DownloadController extends LfmController
{
    /**
     * Download a file.
     *
     * @return mixed
     */
    public function getDownload()
    {
        return response()->download(parent::getCurrentPath(request('file')));
    }
}
