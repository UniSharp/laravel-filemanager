## Documents
  1. [Installation](https://unisharp.github.io/laravel-filemanager/installation)
  1. [Integration](https://unisharp.github.io/laravel-filemanager/integration)
  1. [Config](https://unisharp.github.io/laravel-filemanager/config)
  1. [Customization](https://unisharp.github.io/laravel-filemanager/customization)
  1. [Events](https://unisharp.github.io/laravel-filemanager/events)
  1. [Upgrade](https://unisharp.github.io/laravel-filemanager/upgrade)

## Customization
Feel free to customize the routes and views if your need.

### Routes
1. Copy the routes in `/vendor/unisharp/laravel-filemanager/src/routes.php`

1. Make sure urls below is correspond to your route (remember to include type parameter `?type=Images` or `?type=Files`) :

 * CKEditor

    ```javascript
    <script>
      CKEDITOR.replace('editor', {
        filebrowserImageBrowseUrl: '/your-custom-route?type=Images',
        filebrowserBrowseUrl: '/your-custom-route?type=Files',
      });
    </script>
    ```  

 * TinyMCE
 
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
Copy views to `resources/views/vendor/unisharp/laravel-filemanager/` :

```bash
php artisan vendor:publish --tag=lfm_view
```

### Translations

1. Copy `vendor/unisharp/laravel-filemanager/src/lang/en` to `/resources/lang/vendor/laravel-filemanager/<YOUR LANGUAGE>/lfm.php`.
1. Edit the file as you please.
