# laravel-filemanager

A file upload/editor intended for use with [Laravel 5](http://www.laravel.com/ "Title") and [CKEditor](http://ckeditor.com/).

## Rationale

There are other packages out there that offer much the same functionality, such as [KCFinder](http://kcfinder.sunhater.com/),
but I found that integration with Laravel was a bit problematic, particularly when it comes to handling sessions
and security.

This package is written specifically for Laravel 5, and will integrate seamlessly.

## Requirements

1. This package only supports Laravel 5.x
1. Requires `"intervention/image": "2.*"`
1. Requires `"francodacosta/phmagick": "0.4.*@dev"`
1. Requires PHP 5.5 or later

## Installation

1. Installation is done through composer and packagist. From within your project root directory, execute the 
following command:

    `composer require tsawler/laravel-filemanager`

1. Then run `composer update` and add the ServiceProvider to the providers array in config/app.php:

    `'Tsawler\Laravelfilemanager\LaravelFilemanagerServiceProvider',`

1. Next publish the config file:

    `php artisan vendor:publish --tag=lfm_config`

1. By default, the package will use its own routes. If you don't want to use those routes (and you probably don't,
since they do not enforce any kind of security), change this entry in config/lfm.php to false:

    ```php
        'use_package_routes' => true,
    ```
