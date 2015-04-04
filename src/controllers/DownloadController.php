<?php namespace Tsawler\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

/**
 * Class LfmController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class DownloadController extends Controller {

    /**
     * @var
     */
    protected $file_location;


    /**
     * constructor
     */
    function __construct()
    {
        if (Session::get('lfm_type') == "Images")
            $this->file_location = Config::get('lfm.images_dir');
        else
            $this->file_location = Config::get('lfm.files_dir');
    }


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
