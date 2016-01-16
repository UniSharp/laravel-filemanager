# laravel-filemanager

PR is welcome.

## Overview

 * The project was forked from [tsawler/laravel-filemanager](http://packalyst.com/packages/package/tsawler/laravel-filemanager)
 * Support public and private folders for multi users
 * Customizable views, routes and middlewares
 * Supported locales : en, fr, zh-TW, zh-CN, pt-BR

## Requirements

 * php >= 5.5
 * Laravel 5
 * requires [intervention/image](https://github.com/Intervention/image)(to make thumbs, crop and resize images).

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

1. Publish the package's config and assets :

    ```bash
        php artisan vendor:publish --tag=lfm_config
        php artisan vendor:publish --tag=lfm_public
    ```

1. Implementation:
    CKEditor
    ```javascript
        <script>
            CKEDITOR.replace( 'editor', {
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
                filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'
            });
        </script>
    ```

    TinyMCE 4
    ```javascript
        <script>
            var editor_config = {
                path_absolute : "http://path_to_filemanager/",
                selector: "textarea",
                plugins: [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor colorpicker textpattern"
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
                relative_urls: false,
                file_browser_callback : function(field_name, url, type, win) {
                    var w = window,
                        d = document,
                        e = d.documentElement,
                        g = d.getElementsByTagName('body')[0],
                        x = w.innerWidth || e.clientWidth || g.clientWidth,
                        y = w.innerHeight|| e.clientHeight|| g.clientHeight;

                    var cmsURL = editor_config.path_absolute + 'filemanager/show?&field_name='+field_name+'&lang='+ tinymce.settings.language;

                    if(type == 'image') {
                        cmsURL = cmsURL + "&type=images";
                    }

                    tinyMCE.activeEditor.windowManager.open({
                        file : cmsURL,
                        title : 'Filemanager',
                        width : x * 0.8,
                        height : y * 0.8,
                        resizable : "yes",
                        close_previous : "no"
                    });
                }
            };

            tinymce.init(editor_config);
        </script>
    ```

1. Ensure that the files & images directories(in `config/lfm.php`) are writable by your web server

## Config
    
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

    'user_field'         => 'id',
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

    TinyMCE
    ```javascript
        ...
        var cmsURL = editor_config.path_absolute + 'your-custom-route/show?&field_name='+field_name+'&lang='+ tinymce.settings.language;
        ...
    ```
    
1. To customize the views :

    on Linux :

    ```bash
    cp -rf vendor/unisharp/laravel-filemanager/src/views/* resources/views/vendor/laravel-filemanager/
    ```

    on MAC :

    ```bash
    cp -rf vendor/unisharp/laravel-filemanager/src/views/ resources/views/vendor/laravel-filemanager/
    ```

## Credits
 * All contibutors from GitHub. (issues / PR)
 * Special thanks to
   * [@taswler](https://github.com/tsawler) the author.
   * [@welcoMattic](https://github.com/welcoMattic) providing fr locale and lots of bugfixes.
   * [@olivervogel](https://github.com/olivervogel) for the awesome [image library](https://github.com/Intervention/image)
   * [@UniSharp members](https://github.com/UniSharp)
