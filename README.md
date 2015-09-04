# laravel-filemanager

## Overview

Fork from [tsawler/laravel-filemanager](http://packalyst.com/packages/package/tsawler/laravel-filemanager), add mechanism to restrict users to see only their own folders.
The original functions support image and file upload, this package only modifies the image functions.

## Requirements

This package requires `"intervention/image": "2.*"`, in order to make thumbs, crop and resize images.

## Installation

1. Run `composer require intervention/image`

1. Run `composer require unisharp/laravel-filemanager`

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
    
1. Fill user_field with your user slug in config/lfm.php :
 
    ```
        'user_field' => "\Auth::user()->name",
    ```

1. View initiation

    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images'
            });
        </script>
    ```

    Or initiate using ckeditor jquery adapter

    ```javascript
        <script>
            $('textarea').ckeditor({
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images'
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
        'images_dir'         => 'public/vendor/laravel-filemanager/images/',
        'images_url'         => '/vendor/laravel-filemanager/images/',
    ```

1. If the route is changed, make sure `filebrowserImageBrowseUrl` is correspond to your route :

    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserImageBrowseUrl: '/your-custom-route?type=Images'
            });
        </script>
    ```
    
    And be sure to include the `?type=Images` parameter.
    
1. To customize the views, run `php artisan vendor:publish --tag=lfm_views`
