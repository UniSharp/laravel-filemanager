## Routes
1. Edit `routes/web.php` :

    Create route group to wrap package routes.

    ```php
    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
    ```

    Make sure `auth` middleware is present to :

    1. prevent unauthorized uploads
    1. work properly with multi-user mode

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

## Views
Copy views to `resources/views/vendor/unisharp/laravel-filemanager/` :

```bash
php artisan vendor:publish --tag=lfm_view
```

## Translations

1. Copy `vendor/unisharp/laravel-filemanager/src/lang/en` to `/resources/lang/vendor/laravel-filemanager/<YOUR LANGUAGE>/lfm.php`.
1. Edit the file as you please.
