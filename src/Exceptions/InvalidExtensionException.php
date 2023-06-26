<?php

namespace UniSharp\LaravelFilemanager\Exceptions;

class InvalidExtensionException extends \Exception
{
    public function __construct()
    {
        $this->message = trans('laravel-filemanager::lfm.error-invalid');
    }
}
