<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\traits\LfmHelpers;

/**
 * Class LfmController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class LfmController extends Controller
{
    use LfmHelpers;

    public function __construct()
    {
        if (!$this->isProcessingImages() && !$this->isProcessingFiles()) {
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
        $default_folder_type = 'share';
        if ($this->allowMultiUser()) {
            $default_folder_type = 'user';
        }

        return view('laravel-filemanager::index')->with([
            'working_dir'  => $this->rootFolder($default_folder_type),
            'file_type'    => $this->currentLfmType(true),
            'startup_view' => config('lfm.' . $this->currentLfmType() . '_startup_view'),
            'no_extension' => ! extension_loaded('gd') && ! extension_loaded('imagick')
        ]);
    }
}
