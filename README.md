#laravel-filemanager

## Overview

Fork from [tsawler/laravel-filemanager](http://packalyst.com/packages/package/tsawler/laravel-filemanager), add restriction that users can see only their own folders.
The original functions support image and file upload, this package only modifies the image functions.

## Requirements

This package requires `"intervention/image": "2.*"`, in order to make thumbs, crop and resize images.

## Installation

1. Edit `composer.json` file :

    ```json
        "require": {
            "unisharp/laravel-filemanager": "dev-master",
            "intervention/image": "^2.3@dev"
        },
        "repositories": [
            {
                "type": "git",
                "url": "git@bitbucket.org:unisharp/laravel-filemanager.git"
            }
        ],
    ```

2. Run `composer update`

3. Edit `config/app.php` :

    Add this in service providers

    - Laravel 5.0

    ```php
        'Tsawler\Laravelfilemanager\LaravelFilemanagerServiceProvider',
        'Intervention\Image\ImageServiceProvider',
    ```

    - Laravel 5.1

    ```php
        Tsawler\Laravelfilemanager\LaravelFilemanagerServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
    ```

    And add this in class aliases

    - Laravel 5.0

    ```php
        'Image' => 'Intervention\Image\Facades\Image',
    ```

    - Laravel 5.1

    ```php
        'Image' => Intervention\Image\Facades\Image::class,
    ```

4. Edit `Kernel.php` :

    Add this line in routeMiddleware

    - Laravel 5.0

    ```php
        'myfolder' => '\Tsawler\Laravelfilemanager\middleware\OnlySeeMyFolder',
    ```

    - Laravel 5.1

    ```php
        'myfolder' => \Tsawler\Laravelfilemanager\middleware\OnlySeeMyFolder::class,
    ```

5. Publish the package's config and assets :

    `php artisan vendor:publish`

6. View initiation

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

7. Ensure that the files & images directories are writable by your web server

## Customization
    
1. If you want to use your own route, edit config/lfm.php :

    ```php
        'use_package_routes' => false,
    ```
    
2. If you want to specify upload directory, edit config/lfm.php :

    ```php
        'images_dir'         => 'public/vendor/laravel-filemanager/images/',
        'images_url'         => '/vendor/laravel-filemanager/images/',
    ```

3. If the route is changed, make sure `filebrowserImageBrowseUrl` is correspond to your route :

    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images'
            });
        </script>
    ```
    
    And be sure to include the `?type=Images` parameter.
    
