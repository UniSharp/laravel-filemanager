<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

/**
 * Class DownloadController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class DownloadController extends LfmController {

    /**
     * Download a file
     *
     * @return mixed
     */
    public function getDownload()
    {
        $file_to_download = Input::get('file');
        $dir = Input::get('dir');
        return Response::download(base_path()
            .  "/"
            . $this->file_location
            .  $dir
            . "/"
            . $file_to_download);
    }

}
