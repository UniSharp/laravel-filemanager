## Requirements
 * php >= 5.4
 * exif extension
 * fileinfo extension
 * GD Library >=2.0 or Imagick PHP extension >=6.5.7
 * Laravel 5
 * requires [intervention/image](https://github.com/Intervention/image) (to make thumbs, crop and resize images).

## Installation
1. Install package 

    ```bash
    composer require unisharp/laravel-filemanager:~1.8
    ```

1. Edit `config/app.php` :

    \* *For Laravel 5.5 and up, skip to step 3. All service providers and facades are automatically discovered.* 

    Add service providers

    ```php
    Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider::class,
    Intervention\Image\ImageServiceProvider::class,
    ```

    And add class aliases

    ```php
    'Image' => Intervention\Image\Facades\Image::class,
    ```

    Code above is for Laravel 5.1.
    In Laravel 5.0 should leave only quoted class names.

1. Publish the package's config and assets :

    ```bash
    php artisan vendor:publish --tag=lfm_config
    php artisan vendor:publish --tag=lfm_public
    ```
    
1. Run commands to clear cache :
    
    ```bash
    php artisan route:clear
    php artisan config:clear
    ```
    
1. Ensure that the files & images directories (in `config/lfm.php`) are writable by your web server (run commands like `chown` or `chmod`).

## What's next

1. Check the [integration document](http://unisharp.github.io/laravel-filemanager/integration) to see how to apply this package.

1. Check the [config document](http://unisharp.github.io/laravel-filemanager/config) to discover the flexibility of this package.
