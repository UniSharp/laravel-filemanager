## Documents
  1. [Installation](https://unisharp.github.io/laravel-filemanager/installation)
  1. [Integration](https://unisharp.github.io/laravel-filemanager/integration)
  1. [Config](https://unisharp.github.io/laravel-filemanager/config)
  1. [Customization](https://unisharp.github.io/laravel-filemanager/customization)
  1. [Events](https://unisharp.github.io/laravel-filemanager/events)
  1. [Upgrade](https://unisharp.github.io/laravel-filemanager/upgrade)

## Requirements
 * php >= 5.4
 * Laravel 5
 * requires [intervention/image](https://github.com/Intervention/image) (to make thumbs, crop and resize images).

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

    Code above is for Laravel 5.1.
    In Laravel 5.0 should leave only quoted class names.

1. Publish the package's config and assets :

    ```bash
    php artisan vendor:publish --tag=lfm_config
    php artisan vendor:publish --tag=lfm_public
    ```
    
1. Ensure that the files & images directories (in `config/lfm.php`) are writable by your web server(run commands like `chown` or `chmod`).
