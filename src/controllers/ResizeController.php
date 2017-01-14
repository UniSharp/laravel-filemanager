<?php namespace Unisharp\Laravelfilemanager\controllers;

use Intervention\Image\Facades\Image;

/**
 * Class ResizeController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class ResizeController extends LfmController
{
    /**
     * Dipsplay image for resizing
     *
     * @return mixed
     */
    public function getResize()
    {
        $ratio = 1.0;
        $image = request('img');

        $original_image  = Image::make(parent::getCurrentPath($image));
        $original_width  = $original_image->width();
        $original_height = $original_image->height();

        $scaled = false;

        if ($original_width > 600) {
            $ratio  = 600 / $original_width;
            $width  = $original_width  * $ratio;
            $height = $original_height * $ratio;
            $scaled = true;
        } else {
            $width  = $original_width;
            $height = $original_height;
        }

        if ($height > 400) {
            $ratio  = 400 / $original_height;
            $width  = $original_width  * $ratio;
            $height = $original_height * $ratio;
            $scaled = true;
        }

        return view('laravel-filemanager::resize')
            ->with('img', parent::getFileUrl($image))
            ->with('height', number_format($height, 0))
            ->with('width', $width)
            ->with('original_height', $original_height)
            ->with('original_width', $original_width)
            ->with('scaled', $scaled)
            ->with('ratio', $ratio);
    }

    public function performResize()
    {
        $img    = request('img');
        $dataX  = request('dataX');
        $dataY  = request('dataY');
        $height = request('dataHeight');
        $width  = request('dataWidth');

        try {
            Image::make(public_path() . $img)->resize($width, $height)->save();
            return "OK";
        } catch (Exception $e) {
            return "width : " . $width . " height: " . $height;
            return $e;
        }
    }
}
