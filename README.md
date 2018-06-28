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

## v1.8 released
 * Please follow the intructions in [upgrade document](https://unisharp.github.io/laravel-filemanager/upgrade).
 * Important changes :
   * Fix Windows compatibility (utf-8 file names and folder names).
   * New feature : Copy & Crop. Thanks [gwleuverink](https://github.com/gwleuverink).
   * [Config document](https://unisharp.github.io/laravel-filemanager/config) is refactored.

## Security

It is important to note that if you use your own routes **you must protect your routes to Laravel-Filemanager in order to prevent unauthorized uploads to your server**. Fortunately, Laravel makes this very easy.

If, for example, you want to ensure that only logged in users have the ability to access the Laravel-Filemanager, simply wrap the routes in a group, perhaps like this:

```php
Route::group(['middleware' => 'auth'], function () {
    Route::get('/laravel-filemanager', '\UniSharp\LaravelFilemanager\controllers\LfmController@show');
    Route::post('/laravel-filemanager/upload', '\UniSharp\LaravelFilemanager\controllers\UploadController@upload');
    // list all lfm routes here...
});
```

This approach ensures that only authenticated users have access to the Laravel-Filemanager. If you are using Middleware or some other approach to enforce security, modify as needed.

**If you use the laravel-filemanager default route, make sure the `auth` middleware (set in config/lfm.php) is enabled and functional**.

## v2.0 progress
* [x] (done) Unit test
* [x] (done) Integrate with Laravel Storage
* [x] (done) Multiple selection
* [ ] Configurable disk of storage
* [ ] (in progress) Responsive design
* [ ] (in progress) Config refactoring
* [x] (done) JSON APIs
* [ ] Move to folder function
* [ ] Applying MIME icon generator
* [x] (done) Bootstrap 4 support


## Contributors & Credits

### Developers / Maintainers

 * [Stream](https://github.com/g0110280)
 * [@gwleuverink](https://github.com/gwleuverink)
 * All [@UniSharp](https://github.com/UniSharp) members

### Contributors

 * [All contibutors](https://github.com/UniSharp/laravel-filemanager/graphs/contributors) from GitHub. (issues / PR)
 * [@taswler](https://github.com/tsawler) the original author of this package.
 * Nathan for providing security suggestions.

### Credits

 * [@olivervogel](https://github.com/olivervogel) for the awesome [image library](https://github.com/Intervention/image).
 * SVG Loaders by [Sam](http://samherbert.net/svg-loaders/) (Licensed MIT)

