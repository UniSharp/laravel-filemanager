<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

/**
 * Class RedirectController.
 */
class RedirectController extends LfmController
{
    private $file_path;

    public function __construct()
    {
        $delimiter = config('lfm.url_prefix', config('lfm.prefix')). '/';
        $url = urldecode(request()->url());
        $external_path = substr($url, strpos($url, $delimiter) + strlen($delimiter));

        $this->file_path = base_path(config('lfm.base_directory', 'public') . '/' . $external_path);
    }

    /**
     * Get image from custom directory by route.
     *
     * @param string $image_path
     * @return string
     */
    public function getImage($base_path, $image_name)
    {
        return $this->responseImageOrFile();
    }

    /**
     * Get file from custom directory by route.
     *
     * @param string $file_name
     * @return string
     */
    public function getFile(Request $request, $base_path, $file_name)
    {
        $request->request->add(['type' => 'Files']);

        return $this->responseImageOrFile();
    }

    private function responseImageOrFile()
    {
        $file_path = $this->file_path;

        if (! File::exists($file_path)) {
            abort(404);
        }

        $file = File::get($file_path);
        $type = parent::getFileType($file_path);

        $response = Response::make($file);
        $response->header('Content-Type', $type);

        return $response;
    }
}
