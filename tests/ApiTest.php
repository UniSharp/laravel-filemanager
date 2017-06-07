<?php

use Illuminate\Http\UploadedFile;

class ApiTest extends TestCase
{
    /**
     * 測試資料夾 API
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
     * 上傳一筆檔案
     *
     * @group image
     */
    public function testUploadImage()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->json('GET', route('unisharp.lfm.upload'), [
            'upload' => [$file]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'created' => 'OK',
        ]);

        $filename = json_decode($response->getContent())->filenames[0];
        $thumb_filename = substr_replace($filename, '_S', strrpos($filename, '.'), 0);
        $file_path = $this->getStoragedFilePath($filename);
        $thumb_file_path = $this->getStoragedFilePath($thumb_filename);
        $this->assertFileExists($file_path);
        $this->assertFileExists($thumb_file_path);

        @unlink($file_path);
        @unlink($thumb_file_path);
    }

    /**
     * 刪除檔案
     *
     * @group image
     */
    // public function testUploadImageWithInDirectory()
    // {
    //     $file = UploadedFile::fake()->image('test.jpg');

    //     $add_file_response = $this->json('GET', route('unisharp.lfm.upload'), [
    //         'upload' => [$file]
    //     ]);
    //     $filename = json_decode($add_file_response->getContent())->filenames[0];
    //     $thumb_filename = substr_replace($filename, '_S', strrpos($filename, '.'), 0);

    //     $response->json('GET', route('unisharp.lfm.getDelete'), [
    //         'items' => $filename
    //     ]);

    //     $response->assertStatus(200);

    //     $file_path = $this->getStoragedFilePath($filename, $working_dir);
    //     $thumb_file_path = $this->getStoragedFilePath($thumb_filename, $working_dir);
    //     $this->assertFileNotExists($file_path);
    //     $this->assertFileNotExists($thumb_file_path);
    // }
}
