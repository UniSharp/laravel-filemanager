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

1. Next run composer update to install the package from packagist:

    `composer update`

1. Add the ServiceProvider to the providers array in config/app.php:

    `'Tsawler\Laravelfilemanager\LaravelFilemanagerServiceProvider',`

1. Publish the package's config file:

    `php artisan vendor:publish --tag=lfm_config`

1. Publish the package's public folder assets:

    `php artisan vendor:publish --tag=lfm_public`
    
1. If you want to customize the views, then publish the package's views:

    `php artisan vendor:publish --tag=lfm_views`
    
1. By default, the package will use its own routes. If you don't want to use those routes (and you probably don't,
since they do not enforce any kind of security), change this entry in config/lfm.php to false:

    ```php
        'use_package_routes' => true,
    ```
    
1. If you don't want to use the default image directory or url, update the appropriate lines in config/lfm.php:

    ```php
        'images_dir'         => 'public/vendor/laravel-filemanager/files/',
        'images_url'         => '/vendor/laravel-filemanager/files/',
    ```
    
1. In the view where you are using a CKEditor instance, use the file uploader by initializing the
CKEditor instance as follows:

    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserBrowseUrl: '/laravel-filemanager?type=Images'
            });
        </script>
    ```
    
    Here, "editor" is the id of the textarea you are transforming to a CKEditor instance. Note that if
    you are using a custom route you will have to change `/laravel-filemanager?type=Images` to correspond
    to whatever route you have chosen. Be sure to include the `?type=Images` parameter.
    
    
## Security

It is important to note that __you must protect your routes to Laravel-Filemanager in order to prevent
unauthorized uploads to your server__. Fortunately, Laravel makes this very easy.

If you only want to ensure that logged in users have the ability to access the Laravel-Filemanager, 
simply wrap the routes in a group, perhaps like this:

    Route::group(array('before' => 'auth'), function ()
    {
        Route::get('/laravel-filemanager', 'Tsawler\Laravelfilemanager\controllers\LfmController@show');
        Route::post('/laravel-filemanager/upload', 'Tsawler\Laravelfilemanager\controllers\LfmController@upload');
        Route::get('/laravel-filemanager/data', 'Tsawler\Laravelfilemanager\controllers\LfmController@getData');
        Route::get('/laravel-filemanager/delete', 'Tsawler\Laravelfilemanager\controllers\LfmController@getDelete');
        Route::get('/laravel-filemanager/picsjson', 'Tsawler\Laravelfilemanager\controllers\LfmController@getImages');
        Route::get('/laravel-filemanager/newfolder', 'Tsawler\Laravelfilemanager\controllers\LfmController@getAddfolder');
        Route::get('/laravel-filemanager/deletefolder', 'Tsawler\Laravelfilemanager\controllers\LfmController@getDeletefolder');

    });
    
This approach ensures that only authenticated users have access to the Laravel-Filemanager. If you are
using Middleware or some other approach to enforce security, modify as needed.
    
## License

This package is released under the terms of the [MIT License](http://en.wikipedia.org/wiki/MIT_License).
