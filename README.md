# laravel-filemanager

A files and images management user interface with file uploading support. (Works well with CKEditor and TinyMCE)

PR is welcome!

## Overview

 * The project was forked from [tsawler/laravel-filemanager](http://packalyst.com/packages/package/tsawler/laravel-filemanager)
 * Support public and private folders for multi users
 * Customizable views, routes and middlewares
 * Supported locales : en, fr, pt-BR, tr, zh-CN, zh-TW


## Requirements

 * php >= 5.5
 * Laravel 5
 * requires [intervention/image](https://github.com/Intervention/image) (to make thumbs, crop and resize images).

## Notes

 * For `laravel 5.2`, please set `'middlewares' => ['web', 'auth'],` in config/lfm.php
 * With laravel-filemanager >= 1.3.0, the new configs `valid_image_mimetypes` and `valid_file_mimetypes` restrict the MIME types of the uploading files.

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
    
1. Ensure that the files & images directories (in `config/lfm.php`) are writable by your web server.

## WYSIWYG Editor Integration:
### Option 1: CKEditor

  1. Install [laravel-ckeditor](https://github.com/UniSharp/laravel-ckeditor) package

  1. Modify the views
      
    Sample 1 - Replace by ID:
    ```html
    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <textarea id="my-editor" name="content" class="form-control">{!! old('content', $content) !!}</textarea>
    <script>
      CKEDITOR.replace( 'my-editor', {
        filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
        filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'
      });
    </script>
    ```
    
    Sample 2 - With JQuery Selector:
    
    ```html
    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script src="/vendor/unisharp/laravel-ckeditor/adapters/jquery.js"></script>
    <textarea name="content" class="form-control my-editor">{!! old('content', $content) !!}</textarea>
    <script>
      $('textarea.my-editor').ckeditor({
        filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
        filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'
      });
    </script>
    ```

### Option 2: TinyMCE4

```html
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<textarea name="content" class="form-control my-editor">{!! old('content', $content) !!}</textarea>
<script>
  var editor_config = {
    path_absolute : "/",
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
      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

      var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
      if (type == 'image') {
        cmsURL = cmsURL + "&type=Images";
      } else {
        cmsURL = cmsURL + "&type=Files";
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

### Option 3: Independent use

If you are going to use filemanager independently,meaning set the value of an input to selected photo/file url,follow this structure:

1. create a popup window or modal(whatever you like):

```html
    <a href="/laravel-filemanager" id="feature-img-container"><img src="no_photo.jpg"></a>
	<input name="thumbnail" type="hidden" id="thumbnail">
```	

```javascript
    $('#feature-img-container').on('click', function(e)
   {

        window.open(this.href, 'Filemanager', 'width=900,height=600');

        return false;
   });
```

2. define a function named `SetUrl`:

```javascript
    function SetUrl(url){

	  //set the value of the desired input to image url,often this is a hidden input
      $('#thumbnail').val(url);
	  
	  //set or change the feature image src,recall wordpress feature image
      $('#feature-img-container').find('img').attr('src',url);
    }
```


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
    // NOTE: for laravel 5.2, please use ['web', 'auth']

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


    // valid image mimetypes
    'valid_image_mimetypes' => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/gif'
    ],


    // valid file mimetypes (only when '/laravel-filemanager?type=Files')
    'valid_file_mimetypes' => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'text/plain'
    ],
```

## Customization

1. If the route is changed, make sure urls below is correspond to your route :

  CKEditor
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
        var cmsURL = editor_config.path_absolute + 'your-custom-route?field_name='+field_name+'&lang='+ tinymce.settings.language;
        if (type == 'image') {
          cmsURL = cmsURL + "&type=Images";
        } else {
          cmsURL = cmsURL + "&type=Files";
        }
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

## Screenshots
![FileManager screenshot 1](http://unisharp.com/img/filemanager1.png)
![FileManager screenshot 2](http://unisharp.com/img/filemanager2.png)

## Credits
 * All contibutors from GitHub. (issues / PR)
 * Special thanks to
   * [@taswler](https://github.com/tsawler) the original author.
   * [@welcoMattic](https://github.com/welcoMattic) providing fr translations and lots of bugfixes.
   * [@fraterblack](https://github.com/fraterblack) TinyMCE 4 support and pt-BR translations.
   * [@1dot44mb](https://github.com/1dot44mb) tr translations.
   * [@olivervogel](https://github.com/olivervogel) for the awesome [image library](https://github.com/Intervention/image)
   * [All @UniSharp members](https://github.com/UniSharp)
