<?php

namespace UniSharp\LaravelFilemanager\controllers;

use Unisharp\FileApi\FileApi;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UniSharp\LaravelFilemanager\Events\ImageIsUploading;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;

class UploadController extends LfmController
{
    private $driver;
    private $thumb_driver;

    public function __construct()
    {
        $this->driver = new FileApi($this->lfm->path('storage'));
        $this->thumb_driver = new FileApi($this->lfm->thumb()->path('storage'));

        parent::__construct();
    }

    /**
     * Upload an image/file and (for images) create thumbnail.
     *
     * @param UploadRequest $request
     * @return string
     */
    public function upload()
    {
        $uploaded_files = request()->file('upload');
        $error_bag = [];

        foreach (is_array($uploaded_files) ? $uploaded_files : [$uploaded_files] as $file) {
            $validation_message = $this->uploadValidator($file);
            if ($validation_message !== 'pass') {
                array_push($error_bag, $validation_message);
                continue;
            }

            $new_filename = $this->proceedSingleUpload($file);
        }

        if (is_array($uploaded_files)) {
            $response = count($error_bag) > 0 ? $error_bag : parent::$success_response;
        } else { // upload via ckeditor 'Upload' tab
            $response = $this->useFile($new_filename);
        }

        return $response;
    }

    private function proceedSingleUpload($file)
    {
        $new_filename = $this->getNewName($file);
        $new_file_path = $this->lfm->setName($new_filename)->path('absolute');

        event(new ImageIsUploading($new_file_path));
        try {
            $new_filename = $this->save($file, $new_filename);
        } catch (\Exception $e) {
            return parent::error('invalid');
        }
        event(new ImageWasUploaded($new_file_path));

        return $new_filename;
    }

    private function uploadValidator($file)
    {
        if (empty($file)) {
            return parent::error('file-empty');
        } elseif (! $file instanceof UploadedFile) {
            return parent::error('instance');
        } elseif ($file->getError() == UPLOAD_ERR_INI_SIZE) {
            $max_size = ini_get('upload_max_filesize');

            return parent::error('file-size', ['max' => $max_size]);
        } elseif ($file->getError() != UPLOAD_ERR_OK) {
            return 'File failed to upload. Error code: ' . $file->getError();
        }

        $new_filename = $this->getNewName($file) . '.' . $file->getClientOriginalExtension();

        if ($this->lfm->setName($new_filename)->exists()) {
            return parent::error('file-exist');
        }

        $mimetype = $file->getMimeType();

        // size to kb unit is needed
        $file_size = $file->getSize() / 1000;
        $type_key = parent::currentLfmType();

        if (config('lfm.should_validate_mime', false)) {
            $mine_config = 'lfm.valid_' . $type_key . '_mimetypes';
            $valid_mimetypes = config($mine_config, []);
            if (false === in_array($mimetype, $valid_mimetypes)) {
                return parent::error('mime') . $mimetype;
            }
        }

        if (config('lfm.should_validate_size', false)) {
            $max_size = config('lfm.max_' . $type_key . '_size', 0);
            if ($file_size > $max_size) {
                return parent::error('size') . $mimetype;
            }
        }

        return 'pass';
    }

    private function getNewName($file)
    {
        $new_filename = parent::translateFromUtf8(trim(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));

        if (config('lfm.rename_file') === true) {
            $new_filename = uniqid();
        } elseif (config('lfm.alphanumeric_filename') === true) {
            $new_filename = preg_replace('/[^A-Za-z0-9\-\']/', '_', $new_filename);
        }

        return $new_filename;
    }

    private function save($file, $new_filename)
    {
        if (parent::fileIsImage($file) && ! parent::imageShouldNotHaveThumb($file)) {
            // create folder for thumbnails
            $this->lfm->thumb()->createFolder();

            // save original image and thumbnails to thumbnail folder
            $new_filename = $this->thumb_driver->thumbs([
                'M' => config('lfm.thumb_img_width', 200) . 'x' . config('lfm.thumb_img_height', 200),
            ])->crop()->save($file, $new_filename);

            // move original image out of thumbnail folder
            $this->lfm->setName($new_filename)->thumb()
                ->move($this->lfm->setName($new_filename));

            // rename thumbnail
            $thumb_name = $this->insertSuffix('_M', $new_filename);
            $this->lfm->setName($thumb_name)->thumb()
                ->move($this->lfm->setName($new_filename)->thumb());

            // delete compress image
            $thumb_name = $this->insertSuffix('_CP', $new_filename);
            $this->lfm->setName($compress_name)->thumb()->delete();
        } else {
            $new_filename = $this->driver->save($file, $new_filename);
        }

        return $new_filename;
    }

    private function insertSuffix($suffix, $file_name)
    {
        return substr_replace($file_name, $suffix, strpos($file_name, '.'), 0);
    }

    private function useFile($new_filename)
    {
        $file = $this->lfm->setName($new_filename)->url();

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
