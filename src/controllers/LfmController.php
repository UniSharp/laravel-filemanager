<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tsawler\Laravelfilemanager\requests\UploadRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;


/**
 * Class LfmController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class LfmController extends Controller {

    /**
     * @return mixed
     */
    public function show()
    {
        if (Input::has('base'))
        {
            $working_dir = Input::get('base');
            $base = "/vendor/laravel-filemanager/files/" . Input::get('base') . "/";
            $files = File::files(base_path('public/vendor/laravel-filemanager/files/'.Input::get('base')));
            $directories = File::directories(base_path('public/vendor/laravel-filemanager/files/'.Input::get('base')));
        }
        else
        {
            $working_dir = "/";
            $base = "/vendor/laravel-filemanager/files/";
            $files = File::files(base_path('public/vendor/laravel-filemanager/files'));
            $directories = File::directories(base_path('public/vendor/laravel-filemanager/files'));
        }

        return View::make('laravel-filemanager::index')
            ->with('files', $files)
            ->with('directories', $directories)
            ->with('base', $base)
            ->with('working_dir', $working_dir);
    }


    /**
     * @param UploadRequest $request
     * @return string
     */
    public function upload(UploadRequest $request)
    {
        $file = Input::file('file_to_upload');
        $working_dir = Input::get('working_dir');

        $destinationPath = base_path() . '/public/vendor/laravel-filemanager/files/';

        if (strlen($working_dir) > 0){
            $destinationPath .= $working_dir . "/";
        }

        $filename = $file->getClientOriginalName();
        $upload_success = Input::file('file_to_upload')->move($destinationPath, $filename);

        if ( ! File::exists($destinationPath . "thumbs"))
        {
            File::makeDirectory($destinationPath . "thumbs");
        }

        $thumb_img = Image::make($destinationPath . $filename);

        $thumb_img->fit(200,200)
            ->save($destinationPath . "thumbs/" . $filename);

        unset($thumb_img);

        if ($working_dir != "/")
            return Redirect::to('/laravel-filemanager?base='.$working_dir);
        else
            return Redirect::to('/laravel-filemanager');
    }


    /**
     * @return mixed
     */
    public function getData()
    {
        $contents = File::files(base_path('public/vendor/laravel-filemanager/files'));
        $directories = File::directories(base_path('public/vendor/laravel-filemanager/files'));

        $dir_array = [];

        // go through all directories
        foreach ($directories as $dir)
        {

            if (basename($dir) != "thumbs")
            {
                $dir_contents = File::files($dir);
                $children = [];

                foreach ($dir_contents as $c)
                {
                    $children[] = ['label' => basename($c), 'id' => Str::slug($dir) . "-" . Str::slug(basename($c))];
                }

                if (sizeof($children) == 0)
                {
                    $children[] = ['label' => '(empty)', 'id' => Str::slug(basename($dir) . '-empty')];
                }

                $dir_array[] = ['label' => basename($dir), 'id' => Str::slug(basename($dir)), 'children' => $children];
            }

        }

        foreach ($contents as $c)
        {
            $dir_array[] = ['label' => basename($c), 'id' => Str::slug(basename($c))];
        }

        return response()->json($dir_array);
    }

    public function getDelete()
    {
        $json = Input::get('items');
        echo Input::get('base');
        echo "<hr>";
        echo $json;
    }

}