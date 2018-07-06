<p align="center"><img src="https://unisharp.github.io/laravel-filemanager/images/logo_type_1.png"></p>

[![Travis CI](https://img.shields.io/travis/UniSharp/laravel-filemanager.svg)](https://travis-ci.org/UniSharp/laravel-filemanager)
[![Total Downloads](https://poser.pugx.org/unisharp/laravel-filemanager/downloads)](https://packagist.org/packages/unisharp/laravel-filemanager)
[![Latest Unstable Version](https://img.shields.io/badge/unstable-v2.0.0--alpha4-orange.svg)](https://packagist.org/packages/unisharp/laravel-filemanager)
[![Latest Stable Version](https://poser.pugx.org/unisharp/laravel-filemanager/v/stable)](https://packagist.org/packages/unisharp/laravel-filemanager)
[![License](https://poser.pugx.org/unisharp/laravel-filemanager/license)](https://packagist.org/packages/unisharp/laravel-filemanager)

 * Document : [unisharp.github.io/laravel-filemanager](http://unisharp.github.io/laravel-filemanager/)
   * [Installation](http://unisharp.github.io/laravel-filemanager/installation)
   * [Integration](http://unisharp.github.io/laravel-filemanager/integration)
   * [Config](http://unisharp.github.io/laravel-filemanager/config)
   * [Customization](http://unisharp.github.io/laravel-filemanager/customization)
   * [Events](http://unisharp.github.io/laravel-filemanager/events)
   * [Upgrade](http://unisharp.github.io/laravel-filemanager/upgrade)
 * Demo : [Laravel Filemanager container](https://github.com/UniSharp/laravel-filemanager-example-5.3)

## Installing alpha version
The alpha version of `v2.0` contains support of cloud storage and fresh new UI with RWD.

 * Run `composer require unisharp/laravel-filemanager:dev-master` to get the latest code.
 * Run `composer require unisharp/laravel-filemanager:v2.0.0-alpha4` to get the latest release of alpha version.

## Errors with namespace
We have changed namespace from `Unisharp` to `UniSharp`, and change the first character of every namespace into capital.

If you are updating this package and encounter any errors like `Class not found`, please remove this package entirely and reinstall again.

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
    Route::get('/laravel-filemanager', '\Unisharp\Laravelfilemanager\controllers\LfmController@show');
    Route::post('/laravel-filemanager/upload', '\Unisharp\Laravelfilemanager\controllers\UploadController@upload');
    // list all lfm routes here...
});
```

This approach ensures that only authenticated users have access to the Laravel-Filemanager. If you are using Middleware or some other approach to enforce security, modify as needed.

**If you use the laravel-filemanager default route, make sure the `auth` middleware (set in config/lfm.php) is enabled and functional**.

## v2.0 progress
* [x] (done) Unit test
* [x] (done) Integrate with Laravel Storage
* [x] (done) Multiple selection
* [x] (done) Responsive design
* [x] (done) Config refactoring
* [x] (done) JSON APIs
* [ ] Move to folder function
* [x] (done) Applying MIME icon generator
* [x] (done) Refactor floating action buttons
* [x] (done) Configurable disk of storage
* [x] (done) Bootstrap 4 support
* [x] (done) Remove bootbox


## Contributors & Credits

### Developers / Maintainers

 * [Stream](https://github.com/g0110280)
 * [@gwleuverink](https://github.com/gwleuverink)
 * All [@UniSharp](https://github.com/UniSharp) members

### Contributors

 * [All contibutors](https://github.com/UniSharp/laravel-filemanager/graphs/contributors) from GitHub. (issues / PR)
 * [@taswler](https://github.com/tsawler) the original author of this package.
 * Nathan for providing security suggestions.
 * [@mdnazmulhasan27771](https://github.com/mdnazmulhasan27771) the designer of our logo.

### Credits

 * [@olivervogel](https://github.com/olivervogel) for the awesome [image library](https://github.com/Intervention/image).
 * SVG Loaders by [Sam](http://samherbert.net/svg-loaders/) (Licensed MIT)

