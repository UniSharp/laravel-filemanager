<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UniSharp\LaravelFilemanager\Exceptions\DuplicateFileNameException;
use UniSharp\LaravelFilemanager\Exceptions\EmptyFileException;
use UniSharp\LaravelFilemanager\Exceptions\ExcutableFileException;
use UniSharp\LaravelFilemanager\Exceptions\FileFailedToUploadException;
use UniSharp\LaravelFilemanager\Exceptions\FileSizeExceedConfigurationMaximumException;
use UniSharp\LaravelFilemanager\Exceptions\FileSizeExceedIniMaximumException;
use UniSharp\LaravelFilemanager\Exceptions\InvalidMimeTypeException;
use UniSharp\LaravelFilemanager\LfmPath;
use UniSharp\LaravelFilemanager\LfmUploadValidator;

function trans()
{
    // leave empty
}

class LfmUploadValidatorTest extends TestCase
{
    public function testPassesSizeLowerThanIniMaximum()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getError')->andReturn(UPLOAD_ERR_OK);

        $validator = new LfmUploadValidator($uploaded_file);

        $this->assertEquals($validator->sizeLowerThanIniMaximum(), $validator);
    }

    public function testFailsSizeLowerThanIniMaximum()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getError')->andReturn(UPLOAD_ERR_INI_SIZE);

        $validator = new LfmUploadValidator($uploaded_file);

        $this->expectException(FileSizeExceedIniMaximumException::class);

        $validator->sizeLowerThanIniMaximum();
    }

    public function testPassesUploadWasSuccessful()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getError')->andReturn(UPLOAD_ERR_OK);

        $validator = new LfmUploadValidator($uploaded_file);

        $this->assertEquals($validator->uploadWasSuccessful(), $validator);
    }

    public function testFailsUploadWasSuccessful()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getError')->andReturn(UPLOAD_ERR_PARTIAL);

        $validator = new LfmUploadValidator($uploaded_file);

        $this->expectException(FileFailedToUploadException::class);

        $validator->uploadWasSuccessful();
    }

    public function testPassesNameIsNotDuplicate()
    {
        $uploaded_file = m::mock(UploadedFile::class);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('setName')->andReturn($lfm_path);
        $lfm_path->shouldReceive('exists')->andReturn(false);

        $validator = new LfmUploadValidator($uploaded_file);

        $this->assertEquals($validator->nameIsNotDuplicate('new_file_name', $lfm_path), $validator);
    }

    public function testFailsNameIsNotDuplicate()
    {
        $uploaded_file = m::mock(UploadedFile::class);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('setName')->andReturn($lfm_path);
        $lfm_path->shouldReceive('exists')->andReturn(true);

        $validator = new LfmUploadValidator($uploaded_file);

        $this->expectException(DuplicateFileNameException::class);

        $validator->nameIsNotDuplicate('new_file_name', $lfm_path);
    }

    public function testPassesIsNotExcutable()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getMimeType')->andReturn('image/jpeg');

        $validator = new LfmUploadValidator($uploaded_file);

        $this->assertEquals($validator->isNotExcutable(), $validator);
    }

    public function testFailsIsNotExcutable()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getMimeType')->andReturn('text/x-php');

        $validator = new LfmUploadValidator($uploaded_file);

        $this->expectException(ExcutableFileException::class);

        $validator->isNotExcutable();
    }

    public function testPassesMimeTypeIsValid()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getMimeType')->andReturn('image/jpeg');

        $validator = new LfmUploadValidator($uploaded_file);

        $this->assertEquals($validator->mimeTypeIsValid(['image/jpeg']), $validator);
    }

    public function testFailsMimeTypeIsValid()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getMimeType')->andReturn('image/jpeg');

        $validator = new LfmUploadValidator($uploaded_file);

        $this->expectException(InvalidMimeTypeException::class);

        $validator->mimeTypeIsValid(['image/png']);
    }

    public function testPassesSizeIsLowerThanConfiguredMaximum()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getSize')->andReturn(500 * 1000);

        $validator = new LfmUploadValidator($uploaded_file);

        $this->assertEquals($validator->sizeIsLowerThanConfiguredMaximum(1000), $validator);
    }

    public function testFailsSizeIsLowerThanConfiguredMaximum()
    {
        $uploaded_file = m::mock(UploadedFile::class);
        $uploaded_file->shouldReceive('getSize')->andReturn(2000 * 1000);

        $validator = new LfmUploadValidator($uploaded_file);

        $this->expectException(FileSizeExceedConfigurationMaximumException::class);

        $validator->sizeIsLowerThanConfiguredMaximum(1000);
    }
}
