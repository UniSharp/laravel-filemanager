<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\controllers\Controller;
use Unisharp\Laravelfilemanager\traits\LfmHelpers;
use Illuminate\Support\Facades\Config;

/**
 * Class LfmController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class LfmController extends Controller
{
    use LfmHelpers;

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

        $startup_view = Config::get('lfm.' . $this->currentLfmType() . '_startup_view');

        return view('laravel-filemanager::index')
            ->with('working_dir', $working_dir)
            ->with('file_type', $this->currentLfmType(true))
            ->with('startup_view', $startup_view)
            ->with('extension_not_found', $extension_not_found);
    }
}
