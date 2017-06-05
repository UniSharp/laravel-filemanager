# Laravel Filemanager
[![Latest Stable Version](https://poser.pugx.org/unisharp/laravel-filemanager/v/stable)](https://packagist.org/packages/unisharp/laravel-filemanager)
[![Total Downloads](https://poser.pugx.org/unisharp/laravel-filemanager/downloads)](https://packagist.org/packages/unisharp/laravel-filemanager)
[![License](https://poser.pugx.org/unisharp/laravel-filemanager/license)](https://packagist.org/packages/unisharp/laravel-filemanager)

 * Document : [unisharp.github.io/laravel-filemanager](http://unisharp.github.io/laravel-filemanager/)
   * [Installation](http://unisharp.github.io/laravel-filemanager/installation)
   * [Integration](http://unisharp.github.io/laravel-filemanager/integration)
   * [Config](http://unisharp.github.io/laravel-filemanager/config)
   * [Customization](http://unisharp.github.io/laravel-filemanager/customization)
   * [Events](http://unisharp.github.io/laravel-filemanager/events)
   * [Upgrade](http://unisharp.github.io/laravel-filemanager/upgrade)
 * Demo : [Laravel Filemanager container](https://github.com/UniSharp/laravel-filemanager-example-5.3)

## v1.7 released
 * Please follow the intructions in [upgrade document](https://unisharp.github.io/laravel-filemanager/upgrade).
 * Important changes :
   * All code refactored.
   * Fix Windows compatibility.
   * Fix file cannot be uploaded to "File Mode".
   * Config file is also refactored, see [config document](https://unisharp.github.io/laravel-filemanager/config).

## Security

It is important to note that if you use your own routes **you must protect your routes to Laravel-Filemanager in order to prevent unauthorized uploads to your server**. Fortunately, Laravel makes this very easy.

If, for example, you want to ensure that only logged in users have the ability to access the Laravel-Filemanager, simply wrap the routes in a group, perhaps like this:

```php
Route::group(array('before' => 'auth'), function ()
{
    Route::get('/laravel-filemanager', '\Unisharp\Laravelfilemanager\controllers\LfmController@show');
    Route::post('/laravel-filemanager/upload', '\Unisharp\Laravelfilemanager\controllers\LfmController@upload');
    // list all lfm routes here...
});
```

This approach ensures that only authenticated users have access to the Laravel-Filemanager. If you are using Middleware or some other approach to enforce security, modify as needed.

**If you use the laravel-filemanager default route, make sure the `auth` middleware (set in config/lfm.php) is enabled and functional**.


## Credits
Special thanks to

 * [All contibutors](https://github.com/UniSharp/laravel-filemanager/graphs/contributors) from GitHub. (issues / PR)
 * [@taswler](https://github.com/tsawler) the original author.
 * [@olivervogel](https://github.com/olivervogel) for the awesome [image library](https://github.com/Intervention/image).
 * All [@UniSharp](https://github.com/UniSharp) members.
