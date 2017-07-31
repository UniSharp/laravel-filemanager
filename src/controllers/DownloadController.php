<?php

namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Http\Request;

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
    public function getDownload(Request $request)
    {
        return response()->download(parent::getCurrentPath($request->input('file')));
    }
}
