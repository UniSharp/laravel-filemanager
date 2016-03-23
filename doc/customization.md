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
