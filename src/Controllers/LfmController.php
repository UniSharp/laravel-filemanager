<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmPath;

class LfmController extends Controller
{
    protected static $success_response = 'OK';

    public function __construct()
    {
        $this->applyIniOverrides();
    }

    /**
     * Set up needed functions.
     *
     * @return object|null
     */
    public function __get($var_name)
    {
        if ($var_name === 'lfm') {
            return app(LfmPath::class);
        } elseif ($var_name === 'helper') {
            return app(Lfm::class);
        }
    }

    /**
     * Show the filemanager.
     *
     * @return mixed
     */
    public function show()
    {
        $key_auth_token = \config('lfm')['key_auth_token'];
        $no_authenticate_redirect_to = \config('lfm')['no_authenticate_token_redirect_to'];

        return view('laravel-filemanager::index', compact('key_auth_token', 'no_authenticate_redirect_to'))
            ->withHelper($this->helper);
    }

    /**
     * Check if any extension or config is missing.
     *
     * @return array
     */
    public function getErrors()
    {
        $arr_errors = [];

        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            array_push($arr_errors, trans('laravel-filemanager::lfm.message-extension_not_found'));
        }

        if (!extension_loaded('exif')) {
            array_push($arr_errors, 'EXIF extension not found.');
        }

        if (!extension_loaded('fileinfo')) {
            array_push($arr_errors, 'Fileinfo extension not found.');
        }

        $mine_config_key = 'lfm.folder_categories.'
            . $this->helper->currentLfmType()
            . '.valid_mime';

        if (!is_array(config($mine_config_key))) {
            array_push($arr_errors, 'Config : ' . $mine_config_key . ' is not a valid array.');
        }

        return $arr_errors;
    }

    /**
     * Overrides settings in php.ini.
     *
     * @return null
     */
    public function applyIniOverrides()
    {
        $overrides = config('lfm.php_ini_overrides', []);

        if ($overrides && is_array($overrides) && count($overrides) === 0) {
            return;
        }

        foreach ($overrides as $key => $value) {
            if ($value && $value != 'false') {
                ini_set($key, $value);
            }
        }
    }


    /**
     * If your use token authenticate, before show media manager call this api for checking authenticate
     * 
     * @return object|null
     * 
     */
    public function checkAuthenticate()
    {
        try {
            $guard_name = \config('lfm.guard_name');

            $auth = \Auth::guard($guard_name);
            if ($auth->check()) {
                $response = [
                    'message' => 'Authorization',
                    'errors' => [],
                    'data' => [
                        'authorization' => true,
                        'redirect_to' => null,
                    ]
                ];
                $status_code  = 200;
            } else {
                $response = [
                    'message' => 'No authorization',
                    'errors' => [],
                    'data' => [
                        'authorization' => false,
                        'redirect_to' => \config('lfm.no_authenticate_token_redirect_to'),
                    ]
                ];
                $status_code = 401;
            }

            return response($response, $status_code);
        } catch (\Exception $e) {
            return \response([
                'message' => 'Error machine',
                'errors' => [
                    'machine' => [$e->getMessage()],
                ],
                'data' => [],
            ], 500);
        }
    }
}
