## Requirements

 * php >= 5.5
 * Laravel 5
 * requires [intervention/image](https://github.com/Intervention/image) (to make thumbs, crop and resize images).

## Notes

 * For `laravel 5.2`, please set `'middlewares' => ['web', 'auth'],` in config/lfm.php
 * With laravel-filemanager >= 1.3.0, the new configs `valid_image_mimetypes` and `valid_file_mimetypes` restrict the MIME types of the uploading files.

## Installation

1. Install package 

    ```bash
        composer require unisharp/laravel-filemanager
    ```

1. Edit `config/app.php` :

    Add service providers

    ```php
        Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider::class,
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
