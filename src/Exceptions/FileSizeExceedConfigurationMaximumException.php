<?php

namespace UniSharp\LaravelFilemanager\Exceptions;

class FileSizeExceedConfigurationMaximumException extends \Exception
{
    public function __construct($file_size)
    {
        $this->message = trans('laravel-filemanager::lfm.error-size') . $file_size;
    }
}
