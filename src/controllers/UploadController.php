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
        $files = Input::file('upload');

        if (is_array($files)) {
            foreach($files as $file)
            {
                $this->proceedSingleUpload($file);
            }

            $response = 'OK';
        } else { // upload via ckeditor 'Upload' tab
            $new_filename = $this->proceedSingleUpload($files);

            $response = $this->useFile($new_filename);
        }

        return $response;
    }

    private function proceedSingleUpload($file)
    {
        $validation_message = $this->uploadValidator($file);
        if ($validation_message !== 'pass') {
            return $validation_message;
        }

        $new_filename = $this->getNewName($file);
        $dest_path = parent::getPath('directory');

        Event::fire(new ImageIsUploading($dest_path . $new_filename));

        try {
            if ($this->isProcessingImages()) {
                Image::make($file->getRealPath())
                    ->orientate() //Apply orientation from exif data
                    ->save($dest_path . $new_filename, 90);

                $this->makeThumb($dest_path, $new_filename);
            } else {
                $file->move($dest_path, $new_filename);
            }
        } catch (\Exception $e) {
            return $this->error('invalid');
        }

        Event::fire(new ImageWasUploaded(realpath($dest_path.'/'.$new_filename)));

        return $new_filename;
    }

    private function uploadValidator($file)
    {
        $is_valid = false;
        $force_invalid = false;

        if (empty($file)) {
            return $this->error('file-empty');
        } elseif (!$file instanceof UploadedFile) {
            return $this->error('instance');
        } elseif ($file->getError() == UPLOAD_ERR_INI_SIZE) {
            $max_size = ini_get('upload_max_filesize');
            return $this->error('file-size', ['max' => $max_size]);
        } elseif ($file->getError() != UPLOAD_ERR_OK) {
            return 'File failed to upload. Error code: ' . $file->getError();
        }

        $new_filename = $this->getNewName($file);
        $dest_path = parent::getPath('directory');

        if (File::exists($dest_path . $new_filename)) {
            return $this->error('file-exist');
        }

        $mimetype = $file->getMimeType();

        // size to kb unit is needed
        $file_size = $file->getSize() / 1000;
        $type_key = $this->currentLfmType();

        $mine_config = 'lfm.valid_' . $type_key . '_mimetypes';
        $valid_mimetypes = Config::get($mine_config, $this->{"default_{$type_key}_types"});
        $max_size = Config::get('lfm.max_' . $type_key . '_size', $this->{"default_max_{$type_key}_size"});

        if (!is_array($valid_mimetypes)) {
            return 'Config : ' . $mine_config . ' is not set correctly';
        }

        if (false === in_array($mimetype, $valid_mimetypes)) {
            return $this->error('mime') . $mimetype;
        }

        if ($file_size > $max_size) {
            return $this->error('size') . $mimetype;
        }

        return 'pass';
    }

    private function getNewName($file)
    {
        $new_filename = trim(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        if (Config::get('lfm.rename_file') === true) {
            $new_filename = uniqid();
        } elseif (Config::get('lfm.alphanumeric_filename') === true) {
            $new_filename = preg_replace('/[^A-Za-z0-9\-\']/', '_', $new_filename);
        }

        $new_file_name_with_extension = $new_filename . '.' . $file->getClientOriginalExtension();

        return $new_file_name_with_extension;
    }

    private function makeThumb($dest_path, $new_filename)
    {
        $thumb_folder_name = Config::get('lfm.thumb_folder_name');
        $thumb_folder_path = $dest_path . $thumb_folder_name;
        $new_file_path     = $dest_path . $new_filename;
        $thumb_image_path  = $dest_path . $thumb_folder_name . '/' . $new_filename;

        if (!File::exists($thumb_folder_path)) {
            File::makeDirectory($thumb_folder_path);
        }

        Image::make($new_file_path)->fit(200, 200)->save($thumb_image_path);
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
