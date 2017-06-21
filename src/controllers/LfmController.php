<?php

namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\LfmPath;
use Unisharp\Laravelfilemanager\traits\LfmHelpers;

class LfmController extends Controller
{
    use LfmHelpers;

    protected static $success_response = 'OK';

    public function __construct()
    {
        $this->applyIniOverrides();
        $this->initHelper();
    }

    public function __get($var_name)
    {
        if ($var_name == 'lfm') {
            return new LfmPath;
        }
    }

    /**
     * Show the filemanager.
     *
     * @return mixed
     */
    public function show()
    {
        // dd(app()::VERSION > "5.1.0");
        return view('laravel-filemanager::index');
    }

    public function getErrors()
    {
        $arr_errors = [];

        if (! extension_loaded('gd') && ! extension_loaded('imagick')) {
            array_push($arr_errors, trans('laravel-filemanager::lfm.message-extension_not_found'));
        }

        $type_key = $this->currentLfmType();
        $mine_config = 'lfm.valid_' . $type_key . '_mimetypes';
        $config_error = null;

        if (! is_array(config($mine_config))) {
            array_push($arr_errors, 'Config : ' . $mine_config . ' is not a valid array.');
        }

        return $arr_errors;
    }
}
