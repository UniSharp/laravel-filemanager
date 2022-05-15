<?php

namespace UniSharp\LaravelFilemanager;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use UniSharp\LaravelFilemanager\Exceptions\DuplicateFileNameException;
use UniSharp\LaravelFilemanager\Exceptions\EmptyFileException;
use UniSharp\LaravelFilemanager\Exceptions\ExcutableFileException;
use UniSharp\LaravelFilemanager\Exceptions\FileFailedToUploadException;
use UniSharp\LaravelFilemanager\Exceptions\FileSizeExceedConfigurationMaximumException;
use UniSharp\LaravelFilemanager\Exceptions\FileSizeExceedIniMaximumException;
use UniSharp\LaravelFilemanager\Exceptions\InvalidMimeTypeException;
use UniSharp\LaravelFilemanager\LfmPath;

class LfmUploadValidator
{
    private $file;

    public function __construct(UploadedFile $file)
    {
        // if (! $file instanceof UploadedFile) {
        //     throw new \Exception(trans(self::PACKAGE_NAME . '::lfm.error-instance'));
        // }

        $this->file = $file;
    }

    // public function hasContent()
    // {
    //     if (empty($this->file)) {
    //         throw new EmptyFileException();
    //     }

    //     return $this;
    // }

    public function sizeLowerThanIniMaximum()
    {
        if ($this->file->getError() == UPLOAD_ERR_INI_SIZE) {
            throw new FileSizeExceedIniMaximumException();
        }

        return $this;
    }

    public function uploadWasSuccessful()
    {
        if ($this->file->getError() != UPLOAD_ERR_OK) {
            throw new FileFailedToUploadException($this->file->getError());
        }

        return $this;
    }

    public function nameIsNotDuplicate($new_file_name, LfmPath $lfm_path)
    {
        if ($lfm_path->setName($new_file_name)->exists()) {
            throw new DuplicateFileNameException();
        }

        return $this;
    }

    public function isNotExcutable($excutable_mimetypes)
    {
        $mimetype = $this->file->getMimeType();

        if (in_array($mimetype, $excutable_mimetypes)) {
            throw new ExcutableFileException();
        }

        return $this;
    }

    public function mimeTypeIsValid($available_mime_types)
    {
        $mimetype = $this->file->getMimeType();

        if (false === in_array($mimetype, $available_mime_types)) {
            throw new InvalidMimeTypeException($mimetype);
        }

        return $this;
    }

    public function sizeIsLowerThanConfiguredMaximum($max_size_in_kb)
    {
        // size to kb unit is needed
        $file_size_in_kb = $this->file->getSize() / 1000;

        if ($file_size_in_kb > $max_size_in_kb) {
            throw new FileSizeExceedConfigurationMaximumException($file_size_in_kb);
        }

        return $this;
    }
}
