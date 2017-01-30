<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\traits\LfmHelpers;

/**
 * Class LfmController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class LfmController extends Controller
{
    use LfmHelpers;

    protected $success_response = 'OK';

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
        $type_key = $this->currentLfmType();
        $mine_config = 'lfm.valid_' . $type_key . '_mimetypes';
        $config_error = null;

        if (!is_array(config($mine_config))) {
            $config_error = 'Config : ' . $mine_config . ' is not set correctly';
        }

        return view('laravel-filemanager::index')->with([
            'url_prefix'   => config('lfm.' . $type_key . 's_url'),
            'file_type'    => $this->currentLfmType(true),
            'success_response' => $this->success_response,
            'lfm_route'    => url(config('lfm.prefix')),
            'lang'         => trans('laravel-filemanager::lfm'),
            'no_extension' => ! extension_loaded('gd') && ! extension_loaded('imagick'),
            'config_error' => $config_error
        ]);
    }
}
