<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

/**
 * Class RedirectController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class RedirectController extends LfmController
{
    /**
     * Get image from custom directory by route
     *
     * @param string $image_path
     * @return string
     */
    public function getImage($base_path, $image_name)
    {
        return $this->responseImageOrFile($image_name);
    }

    /**
     * Get file from custom directory by route
     *
     * @param string $file_name
     * @return string
     */
    public function getFile(Request $request, $base_path, $file_name)
    {
        $request->request->add(['type' => 'Files']);

        return $this->responseImageOrFile($file_name);
    }

    private function responseImageOrFile($file_name)
    {
        $file_path = parent::getCurrentPath($file_name);

        if (!File::exists($file_path)) {
            abort(404);
        }

        $file = File::get($file_path);
        $type = parent::getFileType($file_path);

        $response = Response::make($file);
        $response->header("Content-Type", $type);

        return $response;
    }
}
