## Documents

  1. [Installation](https://github.com/Jayked/laravel-filemanager/blob/master/doc/installation.md)
  1. [Intergration](https://github.com/Jayked/laravel-filemanager/blob/master/doc/integration.md)
  1. [Config](https://github.com/Jayked/laravel-filemanager/blob/master/doc/config.md)
  1. [Customization](https://github.com/Jayked/laravel-filemanager/blob/master/doc/customization.md)

## Requirements

 * php >= 5.5
 * Laravel 5
 * requires [intervention/image](https://github.com/Intervention/image) (to make thumbs, crop and resize images).

## Notes

 * For `laravel 5.2` and up, please set `'middlewares' => ['web', 'auth'],` in config/lfm.php
 * With laravel-filemanager >= 1.0.0, the new configs `valid_image_mimetypes` and `valid_file_mimetypes` restrict the MIME types of the uploading files.

## Installation

1. Install package

    ```shell
        composer require jayked/laravel-filemanager
    ```

1. Edit `config/app.php` :

    Add service providers

    ```php
        Jayked\Laravelfilemanager\LaravelFilemanagerServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
    ```

    And add class aliases

    ```php
        'Image' => Intervention\Image\Facades\Image::class,
    ```

1. Publish the package's config and assets :

    ```bash
        php artisan vendor:publish --tag=lfm_config
        php artisan vendor:publish --tag=lfm_public
    ```
    
1. Ensure that the files & images directories (in `config/lfm.php`) are writable by your web server.
