## Documents

  1. [Installation](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/installation.md)
  1. [Intergration](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/integration.md)
  1. [Config](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/config.md)
  1. [Customization](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/customization.md)
  1. [Events](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/events.md)
  1. [Upgrade](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/upgrade.md)

## Customization

Feel free to customize the routes and views if your need.

### Routes

1. Copy the routes in /vendor/unisharp/laravel-filemanager/src/routes.php

1. Make sure urls below is correspond to your route :

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

### Views

1. Copy the views from /vendor/unisharp/laravel-filemanager/src/views/ :

    ```bash
    php artisan vendor:publish --tag=lfm_view
    ```

### Translations

1. Copy `vendor/unisharp/laravel-filemanager/src/lang/en` to `/resources/lang/vendor/laravel-filemanager/<YOUR LANGUAGE>/lfm.php`
2. Change the file according your preferences
