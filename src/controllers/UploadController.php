<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Lang;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        try {
            $res = $this->uploadValidator();
            if (true !== $res) {
                return "Invalid upload request";
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

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

        if ('Images' === $this->file_type) {
            $this->makeThumb($dest_path, $new_filename);
        }

        // upload via ckeditor 'Upload' tab
        if (!Input::has('show_list')) {
            return $this->useFile($new_filename);
        }

        return 'OK';
    }

    private function uploadValidator()
    {
        // when uploading a file with the POST named "upload"

        $file_array = Input::file();
        $expected_file_type = $this->file_type;
        $is_valid = false;

        if (!is_array($file_array)) {
            throw new \Exception('Incorrect file_array');
        }

        if (!array_key_exists('upload', $file_array)) {
            throw new \Exception('name: "upload" not exists');
        }

        $file = $file_array['upload'];
        if (!$file) {
            throw new \Exception('Unexpected, nothing in "upload"');
        }
        if (!$file instanceof UploadedFile) {
            throw new \Exception('The uploaded file should be an instance of UploadedFile');
        }

        $mimetype = $file->getMimeType();

        // File MimeTypes Check
        $valid_file_mimetypes = Config::get(
            'lfm.valid_file_mimetypes',
            ['application/pdf']
        );
        if (!is_array($valid_file_mimetypes)) {
            throw new \Exception('valid_file_mimetypes is not set correctly');
        }

        if (in_array($mimetype, $valid_file_mimetypes) && $expected_file_type === 'Files') {
            $is_valid = true;
        }

        // Image MimeTypes Check
        $valid_image_mimetypes = Config::get(
            'lfm.valid_image_mimetypes',
            ['image/jpeg', 'image/png', 'image/gif']
        );
        if (!is_array($valid_image_mimetypes)) {
            throw new \Exception('valid_image_mimetypes is not set correctly');
        }
        if (in_array($mimetype, $valid_image_mimetypes)) {
            $is_valid = true;
        }

        if (false === $is_valid) {
            throw new \Exception('Unexpected MimeType: ' . $mimetype);
        }
        return $is_valid;
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
            ->save($dest_path . $thumb_folder_name . DIRECTORY_SEPARATOR . $new_filename);
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
