<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Intervention\Image\Facades\Image;
use UniSharp\LaravelFilemanager\Events\ImageIsRotating;
use UniSharp\LaravelFilemanager\Events\ImageWasRotated;

class RotateController extends LfmController
{
    /**
     * Show rotate page.
     *
     * @return mixed
     */
    public function getRotate()
    {
        return view('laravel-filemanager::rotate')
            ->with([
                'working_dir' => request('working_dir'),
                'img' => $this->lfm->pretty(request('img'))
            ]);
    }

    /**
     * rotate the image (called via ajax).
     */
    public function getRotateimage()
    {
        $image_name = request('img');
        $image_path = $this->lfm->setName($image_name)->path('absolute');
        $rotate_path = $image_path;
        $angle = request('angle');

        event(new ImageIsRotating($image_path));

        // rotate the image
        Image::make($image_path)
            ->orientate()
            ->rotate($angle)
            ->save($rotate_path);


        event(new ImageWasRotated($image_path));

        // make new thumbnail
        $this->lfm->makeThumbnail($image_name);
    }
}
