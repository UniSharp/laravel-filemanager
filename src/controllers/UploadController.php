<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\Event;
use Unisharp\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Lang;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Unisharp\Laravelfilemanager\Events\ImageIsUploading;
use Unisharp\Laravelfilemanager\Events\ImageWasUploaded;

/**
 * Class UploadController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class UploadController extends LfmController {

    private $default_file_types = ['application/pdf'];
    private $default_image_types = ['image/jpeg', 'image/png', 'image/gif'];
    // unit is assumed to be kb
    private $default_max_file_size = 1000;
    private $default_max_image_size = 500;

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
                return Lang::get('laravel-filemanager::lfm.error-invalid');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $files = Input::file('upload');

        foreach($files as $file)
        {
            $new_filename = $this->getNewName($file);
            $dest_path = parent::getPath('directory');

            Event::fire(new ImageIsUploading($dest_path . $new_filename));

            if (File::exists($dest_path . $new_filename)) {
                return Lang::get('laravel-filemanager::lfm.error-file-exist');
            }

            //Apply orientation from exif data
            $img = Image::make($file->getRealPath())->orientate();
            $upload = $img->save($dest_path . $new_filename, 90);

            //$file->move($dest_path, $new_filename);

            if ('Images' === $this->file_type) {
                $this->makeThumb($dest_path, $new_filename);
            }

            Event::fire(new ImageWasUploaded(realpath($dest_path.'/'.$new_filename)));

            // upload via ckeditor 'Upload' tab
            if (!Input::has('show_list')) {
                return $this->useFile($new_filename);
            }
        }

        return 'OK';
    }

    private function uploadValidator()
    {
        // when uploading a file with the POST named "upload"
        $expected_file_type = $this->file_type;
        $is_valid = false;
        $force_invalid = false;

        $files = Input::file('upload');

        foreach($files as $file)
        {
            if (empty($file)) {
                throw new \Exception(Lang::get('laravel-filemanager::lfm.error-file-empty'));
            } elseif (!$file instanceof UploadedFile) {
                throw new \Exception(Lang::get('laravel-filemanager::lfm.error-instance'));
            } elseif ($file->getError() == UPLOAD_ERR_INI_SIZE) {
                $max_size = ini_get('upload_max_filesize');
                throw new \Exception(Lang::get('laravel-filemanager::lfm.error-file-size', ['max' => $max_size]));
            } elseif ($file->getError() != UPLOAD_ERR_OK) {
                dd('File failed to upload. Error code: ' . $file->getError());
            }

            $mimetype = $file->getMimeType();

            // size to kb unit is needed
            $size = $file->getSize() / 1000;

            if ($expected_file_type === 'Files') {
                $mine_config = 'lfm.valid_file_mimetypes';
                $valid_mimetypes = Config::get($mine_config, $this->default_file_types);
                $max_size = Config::get('lfm.max_file_size', $this->default_max_file_size);
            } else {
                $mine_config = 'lfm.valid_image_mimetypes';
                $valid_mimetypes = Config::get($mine_config, $this->default_image_types);
                $max_size = Config::get('lfm.max_image_size', $this->default_max_image_size);
            }

            if (!is_array($valid_mimetypes)) {
                throw new \Exception('Config : ' . $mine_config . ' is not set correctly');
            }

            if (false === in_array($mimetype, $valid_mimetypes)) {
                throw new \Exception(Lang::get('laravel-filemanager::lfm.error-mime') . $mimetype);
            }

            if ($size > $max_size) {
                throw new \Exception(Lang::get('laravel-filemanager::lfm.error-size') . $mimetype);
            }
        }

        return true;
    }

    private function getNewName($file)
    {
        $new_filename = trim(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        // $file_full_name =  $file->getClientOriginalName();

        // $new_filename = substr($file_full_name,0,strrpos($file_full_name,'.'));

        if (Config::get('lfm.rename_file') === true) {
            $new_filename = uniqid();
        } elseif (Config::get('lfm.alphanumeric_filename') === true) {
            $new_filename = preg_replace('/[^A-Za-z0-9\-\']/', '_', $new_filename);
        }

        $new_filename = $new_filename . '.' . $file->getClientOriginalExtension();

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
        $file = parent::getUrl('directory') . $new_filename;

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
