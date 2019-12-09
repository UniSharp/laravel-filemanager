<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Illuminate\Support\Facades\Log;
use UniSharp\LaravelFilemanager\Events\ImageIsUploading;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;
use UniSharp\LaravelFilemanager\Lfm;

class UploadController extends LfmController
{
    protected $errors;

    public function __construct()
    {
        parent::__construct();
        $this->errors = [];
    }

    /**
     * Upload files
     *
     * @param void
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
                Log::error($e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                array_push($error_bag, $e->getMessage());
            }
        }

        if (is_array($uploaded_files)) {
            $response = count($error_bag) > 0 ? $error_bag : array(parent::$success_response);
        } else { // upload via ckeditor 'Upload' tab
            if (is_null($new_filename)) {
                $response = $error_bag[0];
            } else {
                $response = view(Lfm::PACKAGE_NAME . '::use')
                    ->withFile($this->lfm->setName($new_filename)->url());
            }
        }

        return $response;
    }
}
