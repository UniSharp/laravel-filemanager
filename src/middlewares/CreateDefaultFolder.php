<?php

namespace Unisharp\Laravelfilemanager\middlewares;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

class CreateDefaultFolder
{
    public $file_location = null;

    public function handle($request, Closure $next)
    {
        $this->file_type = Input::get('type', 'Images'); // default set to Images.

        if ('Images' === $this->file_type) {
            $this->dir_location = Config::get('lfm.images_url');
            $this->file_location = Config::get('lfm.images_dir');
            $this->startup_view = Config::get('lfm.images_startup_view');
        } elseif ('Files' === $this->file_type) {
            $this->dir_location = Config::get('lfm.files_url');
            $this->file_location = Config::get('lfm.files_dir');
            $this->startup_view = Config::get('lfm.files_startup_view');
        } else {
            throw new \Exception('unexpected type parameter');
        }

        $this->checkDefaultFolderExists('user');
        $this->checkDefaultFolderExists('share');

        return $next($request);
    }

    private function checkDefaultFolderExists($type = 'share')
    {
        if ($type === 'user' && \Config::get('lfm.allow_multi_user') !== true) {
            return;
        }

        $path = $this->getPath($type);

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
    }

    public function getUserSlug()
    {
        return empty(auth()->user()) ? '' : \Auth::user()->user_field;
    }

    private function formatLocation($location, $type = null, $get_thumb = false)
    {
        if ($type === 'share') {
            return $location . Config::get('lfm.shared_folder_name');

        } elseif ($type === 'user') {
            return $location . $this->getUserSlug();

        }

        $working_dir = Input::get('working_dir');

        // remove first slash
        if (substr($working_dir, 0, 1) === '/') {
            $working_dir = substr($working_dir, 1);
        }


        $location .= $working_dir;

        if ($type === 'directory' || $type === 'thumb') {
            $location .= '/';
        }

        //if user is inside thumbs folder there is no need
        // to add thumbs substring to the end of $location
        $in_thumb_folder = preg_match('/'.Config::get('lfm.thumb_folder_name').'$/i',$working_dir);

        if ($type === 'thumb' && !$in_thumb_folder) {
            $location .= Config::get('lfm.thumb_folder_name') . '/';
        }

        return $location;
    }

    public function getPath($type = null, $get_thumb = false)
    {
        $path = base_path() . '/' . $this->file_location;

        $path = $this->formatLocation($path, $type);

        return $path;
    }
}
