<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Unisharp\FileApi\FileApi;

/**
 * Class RedirectController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class RedirectController extends LfmController
{
    public function showFile($file_path)
    {
        $request_url = urldecode(request()->url());
        $storage_path = str_replace(url('/') . '/', '', $request_url);
        $full_path = $this->disk_root . '/' . $storage_path;

        if (!parent::exists($full_path)) {
            abort(404);
        }

        $file = parent::getFile($storage_path);

        $response = Response::make($file);
        $response->header("Content-Type", parent::getFileType($full_path));

        return $response;
    }
}
