# laravel-filemanager

## Overview

 * Fork from [tsawler/laravel-filemanager](http://packalyst.com/packages/package/tsawler/laravel-filemanager)
 * support public and private folders for multi users
 * customizable views and routes
 * supported locales : en, fr(not completed yet), zh-TW, zh-CN

## Requirements

This package requires [intervention/image](https://github.com/Intervention/image), in order to make thumbs, crop and resize images.

## Installation

1. Install package 

    ```
        composer require unisharp/laravel-filemanager
    ```

1. Edit `config/app.php` :

    Add this in service providers

    ```php
        Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
    ```

    And add this in class aliases

    ```php
        'Image' => Intervention\Image\Facades\Image::class,
    ```

1. Publish the package's config and assets :

    ```
        php artisan vendor:publish --tag=lfm_config
    ```
    
1. Fill user_field with a column name in users table as user's slug in `config/lfm.php` :
 
    ```
        'user_field' => 'name',
    ```

1. View initiation

    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images'
                filebrowserBrowseUrl: '/laravel-filemanager?type=Files'
            });
        </script>
    ```

    Or initiate using ckeditor jquery adapter

    ```javascript
        <script>
            $('textarea').ckeditor({
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images'
                filebrowserBrowseUrl: '/laravel-filemanager?type=Files'
            });
        </script>
    ```

1. Ensure that the files & images directories are writable by your web server

## Customization
    
1. To use your own route, edit config/lfm.php :

    ```php
        'use_package_routes' => false,
    ```

1. To disable multi-user mechanism, dit config/lfm.php :

    ```php
        'allow_multi_user' => false,
    ```
    
1. To specify upload directory, edit config/lfm.php :

    ```php
        'images_dir' => 'public/photos/',
        'images_url' => '/photos/',

        'files_dir'  => 'public/files/',
        'files_url'  => '/files/',
    ```

1. If the route is changed, make sure `filebrowserImageBrowseUrl` is correspond to your route :

    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserImageBrowseUrl: '/your-custom-route?type=Images',
                filebrowserBrowseUrl: '/your-custom-route?type=Files',
            });
        </script>
    ```
    
    And be sure to include the `?type=Images` or `?type=Files` parameter.
    
1. To customize the views, copy file from `vendor/unisharp/laravel-filemanager/src/views` to `resources/views/vendor/laravel-filemanager`.
