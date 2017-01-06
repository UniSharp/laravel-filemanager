<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\controllers\Controller;
use Unisharp\Laravelfilemanager\traits\LfmHelpers;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

/**
 * Class LfmController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class LfmController extends Controller
{
    /**
     * @var
     */
    public $startup_view = null;

    use LfmHelpers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->file_type = Input::get('type', 'Images'); // default set to Images.

        if ('Images' === $this->file_type) {
            $this->dir_location = Config::get('lfm.images_url');
            $this->file_location = Config::get('lfm.images_dir');
            $this->startup_view = Config::get('lfm.images_startup_view');
        } elseif ('Files' === $this->file_type) {
            $this->dir_location = Config::get('lfm.files_url');
            $this->file_location = Config::get('lfm.files_dir');
            $this->startup_view = Config::get('lfm.files_startup_view');
        } else {
            throw new \Exception('unexpected type parameter');
        }
    }


    /**
     * Show the filemanager
     *
     * @return mixed
     */
    public function show()
    {
        $working_dir = '/';
        $working_dir .= (Config::get('lfm.allow_multi_user')) ? $this->getUserSlug() : Config::get('lfm.shared_folder_name');

        $extension_not_found = ! extension_loaded('gd') && ! extension_loaded('imagick');

        $startup_view = Config::get('lfm.' . $this->currentProcessingType() . '_startup_view');

        return view('laravel-filemanager::index')
            ->with('working_dir', $working_dir)
            ->with('file_type', $this->file_type)
            ->with('startup_view', $startup_view)
            ->with('extension_not_found', $extension_not_found);
    }
}
