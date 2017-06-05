<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\traits\LfmHelpers;

/**
 * Class LfmController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class LfmController extends Controller
{
    use LfmHelpers;

    protected static $success_response = 'OK';

    public function __construct()
    {
        $this->applyIniOverrides();
    }

    /**
     * Show the filemanager
     *
     * @return mixed
     */
    public function show()
    {
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

        if (!is_array(config($mine_config))) {
            array_push($arr_errors, 'Config : ' . $mine_config . ' is not a valid array.');
        }

        return $arr_errors;
    }
}
