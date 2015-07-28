laravel-filemanager

## Requirements

This package requires `"intervention/image": "2.*"`, in order to make thumbs, crop and resize images.

## Installation

1. Edit `composer.json` file:

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

3. Edit `config/app.php`:

    `'Tsawler\Laravelfilemanager\LaravelFilemanagerServiceProvider',`

4. Publish the package's config, assets, and views :

    `php artisan vendor:publish`
    
1. By default, the package will use its own routes. If you don't want to use those routes (and you probably don't,
since they do not enforce any kind of security), change this entry in config/lfm.php to false:

    ```php
        'use_package_routes' => true,
    ```
    
1. If you don't want to use the default image/file directory or url, update the appropriate lines in config/lfm.php:

    ```php
        'images_dir'         => 'public/vendor/laravel-filemanager/images/',
        'images_url'         => '/vendor/laravel-filemanager/images/',
        'files_dir'          => 'public/vendor/laravel-filemanager/files/',
        'files_url'          => '/vendor/laravel-filemanager/files/',
    ```
    
1. Ensure that the files & images directories are writable by your web serber

1. In the view where you are using a CKEditor instance, use the file uploader by initializing the
CKEditor instance as follows:

    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                filebrowserBrowseUrl: '/laravel-filemanager?type=Files'
            });
        </script>
    ```
    
    Here, "editor" is the id of the textarea you are transforming to a CKEditor instance. Note that if
    you are using a custom route you will have to change `/laravel-filemanager?type=Images` to correspond
    to whatever route you have chosen. Be sure to include the `?type=Images` parameter.
    
