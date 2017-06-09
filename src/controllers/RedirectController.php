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
    private $filename;
    private $fa;

    public function __construct()
    {
        $delimiter = config('lfm.prefix') . '/';
        $url = request()->url();
        // dd($delimiter);
        $external_path = substr($url, strpos($url, $delimiter));
        preg_match('/(.+)\/([^\/]+)/', $external_path, $matches);
        $path = $matches[1];
        $filename = $matches[2];

        $this->fa = new FileApi($path);
        $this->filename = $filename;
    }

    /**
     * Get image from custom directory by route
     *
     * @param string $image_path
     * @return string
     */
    public function getImage($base_path, $image_name)
    {
        // return $this->responseImageOrFile();
        return $this->fa->getResponse($this->filename);
    }

    /**
     * Get file from custom directory by route
     *
     * @param string $file_name
     * @return string
     */
    public function getFile(Request $request, $base_path, $file_name)
    {
        // $request->request->add(['type' => 'Files']);

        // return $this->responseImageOrFile();
        return $this->fa->getResponse($this->filename);
    }

    private function responseImageOrFile()
    {
        $file_path = $this->file_path;

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
