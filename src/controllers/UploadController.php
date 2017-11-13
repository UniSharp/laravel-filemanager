<?php

namespace UniSharp\LaravelFilemanager\controllers;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use UniSharp\LaravelFilemanager\Events\ImageIsUploading;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;
use UniSharp\LaravelFilemanager\Lfm;
use Unisharp\FileApi\FileApi;

class UploadController extends LfmController
{
    public function __construct()
    {
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
                $new_filename = $this->lfm->upload($file);
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
}
