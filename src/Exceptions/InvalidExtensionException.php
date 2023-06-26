<?php

namespace UniSharp\LaravelFilemanager\Exceptions;

class InvalidExtensionException extends \Exception
{
    public function __construct($ext)
    {
        $this->message = trans('laravel-filemanager::lfm.error-extension') . $ext;
    }
}
