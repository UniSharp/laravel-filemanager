<?php

class ApiTest extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require './bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function testFolder()
    {
        auth()->loginUsingId(1);

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

    private function getResponseByRouteName($route_name, $input = [])
    {
        $response = $this->call('GET', route('unisharp.lfm.' . $route_name), $input);
        $data = json_encode($response);
        return $response->getContent();
    }
}
