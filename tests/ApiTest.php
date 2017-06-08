<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Upload directory name
     *
     * @var String
     */
    protected $dir_name;

    /**
     * Root directory
     *
     * @var String
     */
    protected $root_dir = '/1';

    public function setUp()
    {
        parent::setUp();

        $uniq = uniqid();
        $this->filename = $uniq . '.jpg';
        $this->filename_s = $uniq . '_S.jpg';
        $this->file = UploadedFile::fake()->image($this->filename);

        $this->dir_name = uniqid();
    }

    public function tearDown()
    {
        $storage_path = implode(DIRECTORY_SEPARATOR, [
            config('lfm.base_directory'),
            config('lfm.files_folder_name'),
            (new TestConfigHandler)->userField()
        ]);
        Storage::deleteDirectory($storage_path);
        parent::tearDown();
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
            'upload' => [$this->file],
            'working_dir' => $this->root_dir
        ]);

        $response->assertStatus(200);

        $files_path = $this->getStoragedFilePathWithThumb($this->filename, $this->filename_s, $this->root_dir);
        $this->assertFileExists($files_path['file']);
        $this->assertFileExists($files_path['file_s']);
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
            'upload' => [$this->file],
            'working_dir' => $this->root_dir
        ]);
        $response = $this->json('GET', route('unisharp.lfm.getDelete'), [
            'items' => $this->filename,
            'working_dir' => $this->root_dir
        ]);

        $response->assertStatus(200);

        $files_path = $this->getStoragedFilePathWithThumb($this->filename, $this->filename_s, $this->root_dir);
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
            'upload' => [$this->file],
            'working_dir' => $this->root_dir
        ]);
        $response = $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file],
            'working_dir' => $this->root_dir
        ]);


        $response->assertStatus(200);
        $this->assertEquals($response->getContent(), '["A file with this name already exists!"]');
    }

    /**
     * change file name
     *
     * @group image
     * @group rename
     */
    public function testRenameImage()
    {
        $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file],
            'working_dir' => $this->root_dir
        ]);
        $uniq = uniqid();
        $new_name = $uniq . '.jpg';
        $new_name_s = $uniq . '_S.jpg';
        $response = $this->json('GET', route('unisharp.lfm.getRename'), [
            'file' => $this->filename,
            'new_name' => $new_name,
            'working_dir' => $this->root_dir
        ]);

        $response->assertStatus(200);

        $files_path = $this->getStoragedFilePathWithThumb($new_name, $new_name_s, $this->root_dir);
        $this->assertFileExists($files_path['file']);
        $this->assertFileExists($files_path['file_s']);
    }

    /**
     * add directory
     *
     * @group directory
     */
    public function testAddDirectory()
    {
        $response = $this->json('GET', route('unisharp.lfm.getAddfolder'), [
            'name' => $this->dir_name,
            'working_dir' => $this->root_dir
        ]);

        $response->assertStatus(200);

        $dir_path = $this->getStoragedFilePath($this->dir_name, $this->root_dir);
        $this->assertFileExists($dir_path);
    }

    /**
     * delete directory
     *
     * @group directory
     * @group delete
     */
    public function testDeleteDirectory()
    {
        $this->json('GET', route('unisharp.lfm.getAddfolder'), [
            'name' => $this->dir_name,
            'working_dir' => $this->root_dir
        ]);
        $reponse = $this->json('GET', route('unisharp.lfm.getDelete'), [
            'items' => $this->dir_name,
            'working_dir' => $this->root_dir
        ]);

        $reponse->assertStatus(200);

        $dir_path = $this->getStoragedFilePath($this->dir_name, $this->root_dir);
        $this->assertFileNotExists($dir_path);
    }

    /**
     * rename directory
     *
     * @group directory
     * @group rename
     */
    public function testRenameDirectory()
    {
        $this->json('GET', route('unisharp.lfm.getAddfolder'), [
            'name' => $this->dir_name,
            'working_dir' => $this->root_dir
        ]);
        $new_dir_name = uniqid();
        $response = $this->json('GET', route('unisharp.lfm.getRename'), [
            'file' => $this->dir_name,
            'new_name' => $new_dir_name,
            'working_dir' => $this->root_dir
        ]);

        $response->assertStatus(200);

        $new_dir_path = $this->getStoragedFilePath($new_dir_name, $this->root_dir);
        $this->assertFileExists($new_dir_path);
    }

    /**
     * upload file in a directory
     *
     * @group image
     * @group directory
     */
    public function testUploadFileInDirectory()
    {
        $this->json('GET', route('unisharp.lfm.getAddfolder'), [
            'name' => $this->dir_name,
            'working_dir' => $this->root_dir
        ]);
        $working_dir = $this->root_dir . DIRECTORY_SEPARATOR . $this->dir_name;
        $response = $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file],
            'working_dir' => $working_dir
        ]);

        $response->assertStatus(200);

        $files_path = $this->getStoragedFilePathWithThumb($this->filename, $this->filename_s, $working_dir);
        $this->assertFileExists($files_path['file']);
        $this->assertFileExists($files_path['file_s']);
    }

    /**
     * delete file in a directory
     *
     * @group image
     * @group directory
     */
    public function testDeleteFileInDirectory()
    {
        $this->json('GET', route('unisharp.lfm.getAddfolder'), [
            'name' => $this->dir_name,
            'working_dir' => $this->root_dir
        ]);

        $working_dir = $this->root_dir . DIRECTORY_SEPARATOR . $this->dir_name;
        $response = $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file],
            'working_dir' => $working_dir
        ]);

        $reponse = $this->json('GET', route('unisharp.lfm.getDelete'), [
            'items' => $this->filename,
            'working_dir' => $working_dir
        ]);

        $reponse->assertStatus(200);

        $files_path = $this->getStoragedFilePathWithThumb($this->filename, $this->filename_s, $working_dir);
        $this->assertFileNotExists($files_path['file']);
        $this->assertFileNotExists($files_path['file_s']);
    }

    /**
     * rename file in directory
     *
     * @group image
     * @group directory
     */
    public function testRenameFileInDirectory()
    {
        $this->json('GET', route('unisharp.lfm.getAddfolder'), [
            'name' => $this->dir_name,
            'working_dir' => $this->root_dir
        ]);

        $working_dir = $this->root_dir . DIRECTORY_SEPARATOR . $this->dir_name;
        $response = $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$this->file],
            'working_dir' => $working_dir
        ]);

        $uniq = uniqid();
        $new_name = $uniq . '.jpg';
        $new_name_s = $uniq . '_S.jpg';
        $response = $this->json('GET', route('unisharp.lfm.getRename'), [
            'file' => $this->filename,
            'new_name' => $new_name,
            'working_dir' => $working_dir
        ]);

        $response->assertStatus(200);

        $files_path = $this->getStoragedFilePathWithThumb($new_name, $new_name_s, $working_dir);
        $this->assertFileExists($files_path['file']);
        $this->assertFileExists($files_path['file_s']);
    }

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
