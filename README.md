# laravel-filemanager

## Overview

 * Fork from [tsawler/laravel-filemanager](http://packalyst.com/packages/package/tsawler/laravel-filemanager)
 * support public and private folders for multi users
 * customizable views, routes and middlewares
 * supported locales : en, fr(not completed yet), zh-TW, zh-CN

## Requirements

 * php >= 5.5
 * Laravel 5 (working to support Laravel 4)
 * requires [intervention/image](https://github.com/Intervention/image)(to make thumbs, crop and resize images).

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
        php artisan vendor:publish --tag=lfm_public
    ```
    
1. Set user's folder name (with a column name in users table) in `config/lfm.php` :
 
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

## Setting config
    
In `config/lfm.php` :

    ```php
        'rename_file'        => true,
        // true : files will be renamed as uniqid
        // false : files will remain original names

        'use_package_routes' => true,
        // set this to false to customize route for file manager

        'middlewares'        => ['auth'],
        // determine middlewares that apply to all file manager routes

        'allow_multi_user'   => true,
        // true : user can upload files to shared folder and their own folder
        // false : all files are put together in shared folder

        'user_field'         => 'name',
        // determine which column of users table will be used as user's folder name

        'shared_folder_name' => 'shares',
        // the name of shared folder

        'thumb_folder_name'  => 'thumbs',
        // the name of thumb folder

        'images_dir'         => 'public/photos/',
        'images_url'         => '/photos/',
        // path and url of images

        'files_dir'          => 'public/files/',
        'files_url'          => '/files/',
        // path and url of files
    ```

## Customization

1. If the route is changed, make sure urls below is correspond to your route :

    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserImageBrowseUrl: '/your-custom-route?type=Images',
                filebrowserBrowseUrl: '/your-custom-route?type=Files',
            });
        </script>
    ```
    
    And be sure to include the `?type=Images` or `?type=Files` parameter.
    
1. To customize the views :

    ```
        cp vendor/unisharp/laravel-filemanager/src/views/* resources/views/vendor/laravel-filemanager/
    ```
