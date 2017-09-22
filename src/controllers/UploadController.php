<?php

namespace UniSharp\LaravelFilemanager\controllers;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use UniSharp\LaravelFilemanager\Events\ImageIsUploading;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;
use UniSharp\LaravelFilemanager\Lfm;
use Unisharp\FileApi\FileApi;

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
        $new_filename = null;

        foreach (is_array($uploaded_files) ? $uploaded_files : [$uploaded_files] as $file) {
            try {
                $new_filename = $this->proceedSingleUpload($file);
            } catch (\Exception $e) {
                array_push($error_bag, $e->getMessage());
            }
        }

        if (is_array($uploaded_files)) {
            $response = count($error_bag) > 0 ? $error_bag : parent::$success_response;
        } else { // upload via ckeditor 'Upload' tab
            if (is_null($new_filename)) {
                $response = $error_bag[0];
            } else {
                \Log::info($new_filename);
                \Log::info($this->lfm->setName($new_filename)->url());
                $response = view(Lfm::PACKAGE_NAME . '::use')
                    ->withFile($this->lfm->setName($new_filename)->url());
            }
        }

        return $response;
    }

    private function proceedSingleUpload($file)
    {
        $this->uploadValidator($file);
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
            return parent::error('file-size', ['max' => ini_get('upload_max_filesize')]);
        } elseif ($file->getError() != UPLOAD_ERR_OK) {
            throw new \Exception('File failed to upload. Error code: ' . $file->getError());
        }

        $new_filename = $this->getNewName($file) . '.' . $file->getClientOriginalExtension();

        if ($this->lfm->setName($new_filename)->exists()) {
            return parent::error('file-exist');
        }

        $mimetype = $file->getMimeType();

        $type_key = $this->helper->currentLfmType();

        if (config('lfm.should_validate_mime', false)) {
            $mine_config = 'lfm.valid_' . $type_key . '_mimetypes';
            $valid_mimetypes = config($mine_config, []);
            if (false === in_array($mimetype, $valid_mimetypes)) {
                return parent::error('mime') . $mimetype;
            }
        }

        if (config('lfm.should_validate_size', false)) {
            $max_size = config('lfm.max_' . $type_key . '_size', 0);
            // size to kb unit is needed
            $file_size = $file->getSize() / 1000;
            if ($file_size > $max_size) {
                return parent::error('size') . $file_size;
            }
        }

        return 'pass';
    }

    private function getNewName($file)
    {
        $new_filename = $this->helper->translateFromUtf8(trim(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));

        if (config('lfm.rename_file') === true) {
            $new_filename = uniqid();
        } elseif (config('lfm.alphanumeric_filename') === true) {
            $new_filename = preg_replace('/[^A-Za-z0-9\-\']/', '_', $new_filename);
        }

        return $new_filename;
    }

    private function save($file, $new_filename)
    {
        if ($this->isUploadingImage($file) && $this->shouldCreateThumb($file)) {
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
            $compress_name = $this->insertSuffix('_CP', $new_filename);
            $this->lfm->setName($compress_name)->thumb()->delete();
        } else {
            $new_filename = $this->driver->save($file, $new_filename);
        }

        return $new_filename;
    }

    private function isUploadingImage($file)
    {
        return starts_with($file->getMimeType(), 'image');
    }

    private function shouldCreateThumb($file)
    {
        return !in_array($file->getMimeType(), ['image/gif', 'image/svg+xml']);
    }

    private function insertSuffix($suffix, $file_name)
    {
        return substr_replace($file_name, $suffix, strpos($file_name, '.'), 0);
    }
}
