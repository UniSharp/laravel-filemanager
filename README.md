<p align="center"><img src="https://unisharp.github.io/laravel-filemanager/images/logo_vertical_colored.png"></p>

<p align="center">
  <a target="_blank" href="https://travis-ci.org/UniSharp/laravel-filemanager"><img src="https://img.shields.io/travis/UniSharp/laravel-filemanager.svg"></a>
  <a target="_blank" href="https://packagist.org/packages/unisharp/laravel-filemanager"><img src="https://poser.pugx.org/unisharp/laravel-filemanager/downloads"></a>
  <a target="_blank" href="https://packagist.org/packages/unisharp/laravel-filemanager"><img src="https://img.shields.io/packagist/dm/unisharp/laravel-filemanager.svg"></a>
  <a target="_blank" href="https://scrutinizer-ci.com/g/UniSharp/laravel-filemanager/"><img src="https://scrutinizer-ci.com/g/UniSharp/laravel-filemanager/badges/quality-score.png?b=master"></a>
  <a target="_blank" href="https://codeclimate.com/github/UniSharp/laravel-filemanager/maintainability"><img src="https://api.codeclimate.com/v1/badges/e51f2ef8f4d9f97268db/maintainability" /></a>
  <a target="_blank" href="https://packagist.org/packages/unisharp/laravel-filemanager"><img src="https://img.shields.io/badge/unstable-v2.0.0--alpha8-orange.svg"></a>
  <a target="_blank" href="https://packagist.org/packages/unisharp/laravel-filemanager"><img src="https://poser.pugx.org/unisharp/laravel-filemanager/v/stable"></a>
  <a target="_blank" href="https://packagist.org/packages/unisharp/laravel-filemanager"><img src="https://poser.pugx.org/unisharp/laravel-filemanager/license"></a>
</p>

<p align="center">
  <a href="http://unisharp.github.io/laravel-filemanager/">Documents</a>
・
  <a href="http://unisharp.github.io/laravel-filemanager/installation">Installation</a>
・
  <a href="http://unisharp.github.io/laravel-filemanager/integration">Integration</a>
・
  <a href="http://unisharp.github.io/laravel-filemanager/config">Config</a>
・
  <a href="http://unisharp.github.io/laravel-filemanager/customization">Customization</a>
・
  <a href="http://unisharp.github.io/laravel-filemanager/events">Events</a>
・
  <a href="http://unisharp.github.io/laravel-filemanager/upgrade">Upgrade</a>
・
  <a href="https://github.com/UniSharp/laravel-filemanager-example-5.3">Demo</a>
・
  <a href="https://github.com/UniSharp/laravel-filemanager/wiki">FAQ</a>
</p>

## Installing alpha version
The alpha version of `v2.0` contains support of cloud storage and fresh new UI with RWD.

 * Run `composer require xuandung38/laravel-filemanager:dev-master` to get the latest code.
 * Run `composer require xuandung38/laravel-filemanager:v2.0.0-alpha9` to get the latest release of alpha version.

## v2.0 progress
* [x] (done) Unit test
* [x] (done) Integrate with Laravel Storage
* [x] (done) Multiple selection
* [x] (done) Responsive design
* [x] (done) Config refactoring
* [x] (done) JSON APIs
* [x] (done) Move to folder function
* [x] (done) Applying MIME icon generator
* [x] (done) Refactor floating action buttons
* [x] (done) Configurable disk of storage
* [x] (done) Bootstrap 4 support
* [x] (done) Remove bootbox
* [ ] Documents for v2.0
* [x] (done) Resize function RWD refactor
* [ ] ConfigHandler should overwrite most configs
* [ ] Events should pass object instead of only file path
* [ ] Add more events for files and folders manipulation

## Documents of V1
https://github.com/UniSharp/laravel-filemanager/tree/v1/docs

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
    Route::get('/laravel-filemanager', '\UniSharp\LaravelFilemanager\Controllers\LfmController@show');
    Route::post('/laravel-filemanager/upload', '\UniSharp\LaravelFilemanager\Controllers\UploadController@upload');
    // list all lfm routes here...
});
```

This approach ensures that only authenticated users have access to the Laravel-Filemanager. If you are using Middleware or some other approach to enforce security, modify as needed.

**If you use the laravel-filemanager default route, make sure the `auth` middleware (set in config/lfm.php) is enabled and functional**.

## Contributors & Credits

### Developers / Maintainers

 * [Stream](https://github.com/g0110280)
 * [@gwleuverink](https://github.com/gwleuverink)
 * All [@UniSharp](https://github.com/UniSharp) members

### Contributors

 * [All contibutors](https://github.com/UniSharp/laravel-filemanager/graphs/contributors) from GitHub. (issues / PR)
 * [@taswler](https://github.com/tsawler) the original author of this package.
 * Nathan for providing security suggestions.
 * [@mdnazmulhasan27771](https://github.com/mdnazmulhasan27771) the designer of our logo. (Licensed CC BY 4.0)

### Credits

 * [@olivervogel](https://github.com/olivervogel) for the awesome [image library](https://github.com/Intervention/image).
 * SVG Loaders by [Sam](http://samherbert.net/svg-loaders/) (Licensed MIT)
 * Articles and videos which helped promoting this package.
 * All users and you.
