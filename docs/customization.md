## Documents
  1. [Installation](installation)
  1. [Integration](integration)
  1. [Config](config)
  1. [Customization](customization)
  1. [Events](events)
  1. [Upgrade](upgrade)

## Customization
Feel free to customize the routes and views if your need.

### Routes
1. Copy the routes in `/vendor/unisharp/laravel-filemanager/src/routes.php`

1. Make sure urls below is correspond to your route (remember to include type parameter `?type=Images` or `?type=Files`) :
  * CKEditor
    ```javascript
    CKEDITOR.replace('editor', {
      filebrowserImageBrowseUrl: '/your-custom-route?type=Images',
      filebrowserBrowseUrl: '/your-custom-route?type=Files'
    });
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
