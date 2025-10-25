<?php

namespace UniSharp\LaravelFilemanager\Exceptions;

class FileFailedToUploadException extends \Exception
{
    public function __construct($error_code)
    {
        $this->message = 'File failed to upload. Error code: ' . $error_code;
    }
}
