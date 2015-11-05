<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Lang;
use Intervention\Image\Facades\Image;

/**
 * Class UploadController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class UploadController extends LfmController {

    /**
     * Upload an image/file and (for images) create thumbnail
     *
     * @param UploadRequest $request
     * @return string
     */
    public function upload()
    {
        if (!Input::hasFile('upload')) {
            return Lang::get('laravel-filemanager::lfm.error-file-empty');
        }

        $file = Input::file('upload');

        $new_filename = $this->getNewName($file);

        $dest_path = parent::getPath();

        if (File::exists($dest_path . $new_filename)) {
            return Lang::get('laravel-filemanager::lfm.error-file-exist');
        }

        $file->move($dest_path, $new_filename);

        if (Session::get('lfm_type') == 'Images') {
            $this->makeThumb($dest_path, $new_filename);
        }

        // upload via ckeditor 'Upload' tab
        if (!Input::has('show_list')) {
            return $this->useFile($new_filename);
        }

        return 'OK';
    }

    private function getNewName($file)
    {
        $new_filename = $file->getClientOriginalName();

        if (Config::get('lfm.rename_file') === true) {
            $new_filename = uniqid() . '.' . $file->getClientOriginalExtension();
        }

        return $new_filename;
    }

    private function makeThumb($dest_path, $new_filename)
    {
        $thumb_folder_name = Config::get('lfm.thumb_folder_name');

        if (!File::exists($dest_path . $thumb_folder_name)) {
            File::makeDirectory($dest_path . $thumb_folder_name);
        }

        $thumb_img = Image::make($dest_path . $new_filename);
        $thumb_img->fit(200, 200)
            ->save($dest_path . $thumb_folder_name . '/' . $new_filename);
        unset($thumb_img);
    }

    private function useFile($new_filename)
    {
        $file = parent::getUrl() . $new_filename;

        return "<script type='text/javascript'>

        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);
            return ( match && match.length > 1 ) ? match[1] : null;
        }

        var funcNum = getUrlParam('CKEditorFuncNum');

        var par = window.parent,
            op = window.opener,
            o = (par && par.CKEDITOR) ? par : ((op && op.CKEDITOR) ? op : false);

        if (op) window.close();
        if (o !== false) o.CKEDITOR.tools.callFunction(funcNum, '$file');
        </script>";
    }

}
