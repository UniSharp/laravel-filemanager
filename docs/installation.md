## Requirements
 * php >= 5.4
 * exif extension
 * fileinfo extension
 * GD Library >=2.0 or Imagick PHP extension >=6.5.7
 * Laravel 5
 * requires [intervention/image](https://github.com/Intervention/image) (to make thumbs, crop and resize images).

## TL;DR
1. Run these lines

    ```bash
    composer require unisharp/laravel-filemanager
    php artisan vendor:publish --tag=lfm_config
    php artisan vendor:publish --tag=lfm_public
    php artisan storage:link
    ```

1. Edit `APP_URL` in `.env`.

## Full Installation Guide
1. Install package

    ```bash
    composer require unisharp/laravel-filemanager
    ```

1. (optional) Edit `config/app.php` :

    \* *For Laravel 5.5 and up, skip to step 3. All service providers and facades are automatically discovered.*

    Add service providers

    ```php
    UniSharp\LaravelFilemanager\LaravelFilemanagerServiceProvider::class,
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

1. (optional) Run commands to clear cache :

    ```bash
    php artisan route:clear
    php artisan config:clear
    ```

1. Ensure that the files & images directories (in `config/lfm.php`) are writable by your web server (run commands like `chown` or `chmod`).

1. Create symbolic link :

    ```bash
    php artisan storage:link
    ```

1. Edit `APP_URL` in `.env`.

1. Edit `routes/web.php` :

    Create route group to wrap package routes.

    ```php
    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
    ```

    Make sure `auth` middleware is present to :

    1. prevent unauthorized uploads
    1. work properly with multi-user mode

1. make sure database exists

1. login and visit `/laravel-filemanager/demo`

## Installing alpha version
 * Run `composer require unisharp/laravel-filemanager:dev-master` to get the latest developer version.

## What's next

1. Check the [integration document](http://unisharp.github.io/laravel-filemanager/integration) to see how to apply this package.

1. Check the [config document](http://unisharp.github.io/laravel-filemanager/config) to discover the flexibility of this package.
