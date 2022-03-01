<?php

namespace UniSharp\LaravelFilemanager\Exceptions;

class InvalidMimeTypeException extends \Exception
{
    public function __construct($mimetype)
    {
        $this->message = trans('laravel-filemanager::lfm.error-mime') . $mimetype;
    }
}
