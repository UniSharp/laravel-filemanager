<?php

use Illuminate\Http\UploadedFile;

class ApiTest extends TestCase
{
    /**
     * Upload file
     *
     * @var UploadedFile
     */
    protected $file;

    /**
     * Upload file name
     *
     * @var String
     */
    protected $filename;

    /**
     * Upload thumble file name
     *
     * @var String
     */
    protected $filename_s;

    public function setUp()
    {
        parent::setUp();

        $uniq = uniqid();
        $this->filename = $uniq . '.jpg';
        $this->filename_s = $uniq . '_S.jpg';
        $this->file = UploadedFile::fake()->image($this->filename);
    }

    /**
     * test directory api
     *
     * @group directory
     */
    public function testFolder()
    {
        // auth()->loginUsingId(1);

        $create = $this->getResponseByRouteName('getAddfolder', [
            'name' => 'testcase'
        ]);

        $create_duplicate = $this->getResponseByRouteName('getAddfolder', [
            'name' => 'testcase'
        ]);

        $create_empty = $this->getResponseByRouteName('getAddfolder', [
            'name' => ''
        ]);

        Config::set('lfm.alphanumeric_directory', true);
        $create_alphanumeric = $this->getResponseByRouteName('getAddfolder', [
            'name' => '測試資料夾'
        ]);

        $rename = $this->getResponseByRouteName('getRename', [
            'file' => 'testcase',
            'new_name' => 'testcase2'
        ]);

        $delete = $this->getResponseByRouteName('getDelete', [
            'items' => 'testcase2'
        ]);

        $this->assertEquals('OK', $create);
        $this->assertEquals(trans('laravel-filemanager::lfm.error-folder-exist'), $create_duplicate);
        $this->assertEquals(trans('laravel-filemanager::lfm.error-folder-name'), $create_empty);
        $this->assertEquals(trans('laravel-filemanager::lfm.error-folder-alnum'), $create_alphanumeric);
        $this->assertEquals('OK', $rename);
        $this->assertEquals('OK', $delete);
    }

    /**
     * upload a file
     *
     * @group image
     */
    public function testUploadImage()
    {
        $response = $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file]
        ]);

        $response->assertStatus(200);

        $files_path = $this->getStoragedFilePath($this->filename, $this->filename_s);
        $this->assertFileExists($files_path['file']);
        $this->assertFileExists($files_path['file_s']);

        $this->unlinkFiles($files_path);
    }

    /**
     * delete a file
     *
     * @group image
     * @group delete
     */
    public function testDeleteImage()
    {
        $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file]
        ]);
        $response = $this->json('GET', route('unisharp.lfm.getDelete'), [
            'items' => $this->filename
        ]);

        $response->assertStatus(200);

        $files_path = $this->getStoragedFilePath($this->filename, $this->filename_s);
        $this->assertFileNotExists($files_path['file']);
        $this->assertFileNotExists($files_path['file_s']);
    }

    /**
     * upload file which exists already
     *
     * @group image
     * @group doubleUpload
     */
    public function testDoubleUpload()
    {
        $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file]
        ]);
        $response = $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file]
        ]);


        $response->assertStatus(200);
        $this->assertEquals($response->getContent(), '["A file with this name already exists!"]');

        $files_path = $this->getStoragedFilePath($this->filename, $this->filename_s);
        $this->unlinkFiles($files_path);
    }

    /**
     * change file name
     *
     * @group image
     * @group
     */

    /**
     * upload file in a directory
     *
     * @group image
     * @group
     */

    /**
     * upload file with lfm.rename_file = true
     *
     * @group image
     * @group
     */

    /**
     * upload file with lfm.alphanumeric_filename = true
     *
     * @group image
     * @group
     */
}
