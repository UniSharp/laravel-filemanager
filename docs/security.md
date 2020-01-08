## Security

It is important to note that if you use your own routes **you must protect your routes to Laravel-Filemanager in order to prevent unauthorized uploads to your server**. Fortunately, Laravel makes this very easy.

If, for example, you want to ensure that only logged in users have the ability to access the Laravel-Filemanager, simply wrap the routes in a group, perhaps like this:

```php
Route::group(['middleware' => 'auth'], function () { // auth middleware is important!
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

This approach ensures that only authenticated users have access to the Laravel-Filemanager. If you are using Middleware or some other approach to enforce security, modify as needed.

**If you use the laravel-filemanager default route, make sure the `auth` middleware (set in config/lfm.php) is enabled and functional**.
