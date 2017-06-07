<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Unisharp\Laravelfilemanager\Events\ImageIsUploading;
use Unisharp\Laravelfilemanager\Events\ImageWasUploaded;
use \Unisharp\FileApi\FileApi;

/**
 * Class UploadController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class UploadController extends LfmController
{
    /**
     * Upload an image/file and (for images) create thumbnail
     *
     * @param UploadRequest $request
     * @return string
     */
    public function upload()
    {
        $files = request()->file('upload');
        $error_bag = [];
        $new_filenames = [];
        foreach (is_array($files) ? $files : [$files] as $file) {
            $validation_message = $this->uploadValidator($file);
            $new_filename = $this->proceedSingleUpload($file);
            $new_filenames[] = $new_filename;

            if ($validation_message !== 'pass') {
                array_push($error_bag, $validation_message);
            } elseif ($new_filename == 'invalid') {
                array_push($error_bag, $response);
            }
        }

        if (is_array($files)) {
            $response = count($error_bag) > 0 ? $error_bag : parent::$success_response;
        } else { // upload via ckeditor 'Upload' tab
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

        $working_dir = parent::getCurrentPath();
        $original_name = preg_replace('/\..+$/', '', $file->getClientOriginalName());
        $file_to_upload = $working_dir . DIRECTORY_SEPARATOR . $original_name;
        $fa = new FileApi($working_dir);

        event(new ImageIsUploading($file_to_upload));
        try {
            if (parent::fileIsImage($file) && !parent::imageShouldNotHaveThumb($file)) {
                $new_filename = $fa
                    ->thumbs([
                        'S' => '96x96'
                    ])->save($file, $original_name);
            } else {
                $new_filename = $fa->save($file, $original_name);
            }
        } catch (\Exception $e) {
            return parent::error('invalid');
        }
        event(new ImageWasUploaded($file_to_upload));

        return $new_filename;
    }

    private function uploadValidator($file)
    {
        $is_valid = false;
        $force_invalid = false;

        if (empty($file)) {
            return parent::error('file-empty');
        } elseif (!$file instanceof UploadedFile) {
            return parent::error('instance');
        } elseif ($file->getError() == UPLOAD_ERR_INI_SIZE) {
            $max_size = ini_get('upload_max_filesize');
            return parent::error('file-size', ['max' => $max_size]);
        } elseif ($file->getError() != UPLOAD_ERR_OK) {
            return 'File failed to upload. Error code: ' . $file->getError();
        }

        $new_filename = $this->getNewName($file);

        if (File::exists(parent::getCurrentPath($new_filename))) {
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

        return $new_filename . '.' . $file->getClientOriginalExtension();
    }

    private function useFile($new_filename)
    {
        $file = parent::getFileUrl($new_filename);

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
