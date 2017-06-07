<?php

use Illuminate\Http\UploadedFile;

class TestCase extends Orchestra\Testbench\TestCase
{
    public function getPackageProviders($app)
    {
        return [
            'Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider',
            'Unisharp\FileApi\FileApiServiceProvider'
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('lfm.use_package_routes', true);

        $app['config']->set('lfm.middlewares', []);

        $app['config']->set('lfm.prefix', 'laravel-filemanager');

        $app['config']->set('lfm.urls_prefix', '');

        $app['config']->set('lfm.allow_multi_user', true);
        $app['config']->set('lfm.allow_share_folder', true);

        $app['config']->set('lfm.user_field', TestConfigHandler::class);

        $app['config']->set('lfm.base_directory', 'public');

        $app['config']->set('lfm.images_folder_name', 'photos');
        $app['config']->set('lfm.files_folder_name', 'files');

        $app['config']->set('lfm.shared_folder_name', 'shares');
        $app['config']->set('lfm.thumb_folder_name', 'thumbs');

        $app['config']->set('lfm.images_startup_view', 'grid');
        $app['config']->set('lfm.files_startup_view', 'list');

        $app['config']->set('lfm.rename_file', false);

        $app['config']->set('lfm.alphanumeric_filename', true);

        $app['config']->set('lfm.alphanumeric_directory', false);

        $app['config']->set('lfm.should_validate_size', false);

        $app['config']->set('lfm.max_image_size', 50000);
        $app['config']->set('lfm.max_file_size', 50000);

        $app['config']->set('lfm.should_validate_mime', false);

        $app['config']->set('lfm.valid_image_mimetypes', [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
        ]);

        $app['config']->set('lfm.valid_file_mimetypes', [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'application/pdf',
            'text/plain',
        ]);

        $app['config']->set('lfm.thumb_img_width', 200);
        $app['config']->set('lfm.thumb_img_height', 200);

        $app['config']->set('lfm.file_type_array', [
            'pdf'  => 'Adobe Acrobat',
            'doc'  => 'Microsoft Word',
            'docx' => 'Microsoft Word',
            'xls'  => 'Microsoft Excel',
            'xlsx' => 'Microsoft Excel',
            'zip'  => 'Archive',
            'gif'  => 'GIF Image',
            'jpg'  => 'JPEG Image',
            'jpeg' => 'JPEG Image',
            'png'  => 'PNG Image',
            'ppt'  => 'Microsoft PowerPoint',
            'pptx' => 'Microsoft PowerPoint',
        ]);

        $app['config']->set('lfm.file_icon_array', [
            'pdf'  => 'fa-file-pdf-o',
            'doc'  => 'fa-file-word-o',
            'docx' => 'fa-file-word-o',
            'xls'  => 'fa-file-excel-o',
            'xlsx' => 'fa-file-excel-o',
            'zip'  => 'fa-file-archive-o',
            'gif'  => 'fa-file-image-o',
            'jpg'  => 'fa-file-image-o',
            'jpeg' => 'fa-file-image-o',
            'png'  => 'fa-file-image-o',
            'ppt'  => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
        ]);

        $app['config']->set('lfm.php_ini_overrides', [
            'memory_limit'        => '256M'
        ]);

        $app['config']->set('fileapi.path', ['/images/event/']);
        $app['config']->set('fileapi.watermark', 'public/img/watermark.png');

        $app['config']->set('fileapi.default_thumbs', ['S' => '96x96', 'M' => '256x256', 'L' => '480x480']);

        $app['config']->set('fileapi.compress_quality', 90);
    }

    public function getResponseByRouteName($route_name, $input = [], $file = [])
    {
        $response = $this->call('GET', route('unisharp.lfm.' . $route_name), $input, $file);
        $data = json_encode($response);
        return $response->getContent();
    }

    protected function getPackageAliases($app)
    {
        return [
            'Image' => 'Intervention\Image\Facades\Image'
        ];
    }

    public function getStoragedFilePath($filename, $working_dir = null)
    {
        $file_path = storage_path(implode(DIRECTORY_SEPARATOR, [
            'app',
            config('lfm.base_directory'),
            config('lfm.files_folder_name'),
            (new TestConfigHandler)->userField(),
            $working_dir,
            $filename
        ]));

        return $file_path;
    }
}
