<?php

namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Unisharp\Laravelfilemanager\Events\ImageIsUploading;
use Unisharp\Laravelfilemanager\Events\ImageWasUploaded;

/**
 * Class UploadController.
 */
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
        $files = request()->file('upload');

        // single file
        if (!is_array($files)) {
            $file = $files;
            if (!$this->fileIsValid($file)) {
                return $this->errors;
            }

            $filename = $this->proceedSingleUpload($file);
            if ($filename === false) {
                return $this->errors;
            }

            // upload via ckeditor 'Upload' tab
            return $this->useFile($filename);
        }


        // Multiple files
        foreach ($files as $file) {
            if (!$this->fileIsValid($file)) {
                continue;
            }
            $this->proceedSingleUpload($file);
        }

        return count($this->errors) > 0 ? $this->errors : parent::$success_response;
    }

    private function proceedSingleUpload($file)
    {
        $new_filename = $this->getNewName($file);
        $new_file_path = parent::getCurrentPath($new_filename);

        event(new ImageIsUploading($new_file_path));
        try {
            if (parent::fileIsImage($file) && !in_array($file->getMimeType(), ['image/gif', 'image/svg+xml'])) {
                // Handle image rotation
                Image::make($file->getRealPath())
                    ->orientate() //Apply orientation from exif data
                    ->save($new_file_path);

                // Generate a thumbnail
                if (parent::imageShouldHaveThumb($file)) {
                    $this->makeThumb($new_filename);
                }
            } else {
                // Create (move) the file
                File::move($file->getRealPath(), $new_file_path);
            }
            chmod($new_file_path, config('lfm.create_file_mode', 0644));
        } catch (\Exception $e) {
            array_push($this->errors, parent::error('invalid'));

            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }

        // TODO should be "FileWasUploaded"
        event(new ImageWasUploaded(realpath($new_file_path)));

        return $new_filename;
    }

    private function fileIsValid($file)
    {
        if (empty($file)) {
            array_push($this->errors, parent::error('file-empty'));
            return false;
        }

        if (! $file instanceof UploadedFile) {
            array_push($this->errors, parent::error('instance'));
            return false;
        }

        if ($file->getError() == UPLOAD_ERR_INI_SIZE) {
            $max_size = ini_get('upload_max_filesize');
            array_push($this->errors, parent::error('file-size', ['max' => $max_size]));
            return false;
        }

        if ($file->getError() != UPLOAD_ERR_OK) {
            $msg = 'File failed to upload. Error code: ' . $file->getError();
            array_push($this->errors, $msg);
            return false;
        }

        $new_filename = $this->getNewName($file);

        if (File::exists(parent::getCurrentPath($new_filename))) {
            array_push($this->errors, parent::error('file-exist'));
            return false;
        }

        $mimetype = $file->getMimeType();

        // Bytes to KB
        $file_size = $file->getSize() / 1024;
        $type_key = parent::currentLfmType();

        if (config('lfm.should_validate_mime', false)) {
            $mine_config = 'lfm.valid_' . $type_key . '_mimetypes';
            $valid_mimetypes = config($mine_config, []);
            if (false === in_array($mimetype, $valid_mimetypes)) {
                array_push($this->errors, parent::error('mime') . $mimetype);
                return false;
            }
        }

        if (config('lfm.should_validate_size', false)) {
            $max_size = config('lfm.max_' . $type_key . '_size', 0);
            if ($file_size > $max_size) {
                array_push($this->errors, parent::error('size'));
                return false;
            }
        }

        return true;
    }

    protected function replaceInsecureSuffix($name)
    {
        return preg_replace("/\.php$/i", '', $name);
    }

    private function getNewName($file)
    {
        $new_filename = parent::translateFromUtf8(trim($this->pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));
        if (config('lfm.rename_file') === true) {
            $new_filename = uniqid();
        } elseif (config('lfm.alphanumeric_filename') === true) {
            $new_filename = preg_replace('/[^A-Za-z0-9\-\']/', '_', $new_filename);
        }

        return $new_filename . $this->replaceInsecureSuffix('.' . $file->getClientOriginalExtension());
    }

    private function makeThumb($new_filename)
    {
        // create thumb folder
        parent::createFolderByPath(parent::getThumbPath());

        // create thumb image
        Image::make(parent::getCurrentPath($new_filename))
            ->fit(config('lfm.thumb_img_width', 200), config('lfm.thumb_img_height', 200))
            ->save(parent::getThumbPath($new_filename));
    }

    private function useFile($new_filename)
    {
        $file = parent::getFileUrl($new_filename);

        $responseType = request()->input('responseType');
        if ($responseType && $responseType == 'json') {
            return [
                "uploaded" => 1,
                "fileName" => $new_filename,
                "url" => $file,
            ];
        }

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

    private function pathinfo($path, $options = null)
    {
        $path = urlencode($path);
        $parts = is_null($options) ? pathinfo($path) : pathinfo($path, $options);
        if (is_array($parts)) {
            foreach ($parts as $field => $value) {
                $parts[$field] = urldecode($value);
            }
        } else {
            $parts = urldecode($parts);
        }

        return $parts;
    }
}
