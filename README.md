# Laravel Filemanager

[![Latest Stable Version](https://poser.pugx.org/unisharp/laravel-filemanager/v/stable)](https://packagist.org/packages/unisharp/laravel-filemanager) [![Total Downloads](https://poser.pugx.org/unisharp/laravel-filemanager/downloads)](https://packagist.org/packages/unisharp/laravel-filemanager) [![Latest Unstable Version](https://poser.pugx.org/unisharp/laravel-filemanager/v/unstable)](https://packagist.org/packages/unisharp/laravel-filemanager) [![License](https://poser.pugx.org/unisharp/laravel-filemanager/license)](https://packagist.org/packages/unisharp/laravel-filemanager)

A files and images management user interface with file uploading support. (Works well with CKEditor and TinyMCE)

PR is welcome!

## Overview

 * The project was forked from [tsawler/laravel-filemanager](http://packalyst.com/packages/package/tsawler/laravel-filemanager)
 * Customizable routes and middlewares
 * Supported locales : en, fr, pt-BR, tr, zh-CN, zh-TW
 * Supports public and private folders for multi users
 * Supports multi-level folders
 * Supports using independently(see integration doc)

## Documents

  1. [Installation](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/installation.md)
  1. [Intergration](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/integration.md)
  1. [Config](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/config.md)
  1. [Customization](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/customization.md)

## Upgrade guide
  * `composer update unisharp/laravel-filemanager`
  * `php artisan vendor:publish --tag=lfm_view --force`
  * `php artisan vendor:publish --tag=lfm_config --force` (IMPORTANT: please backup your own `config/lfm.php` first)

## Screenshots
  * Independent usage example :

![Independent usage example](http://unisharp.github.io/images/lfm01.png)

  * List view :

![FileManager screenshot 1](http://unisharp.com/img/filemanager1.png)

  * Grid view :

![FileManager screenshot 2](http://unisharp.com/img/filemanager2.png)

## Events

To use events you can add a listener to listen to the events

Snippet for `EventServiceProvider`
```php
    protected $listen = [
        ImageWasUploaded::class => [
            UploadListener::class,
        ],
    ];
```

The `UploadListener` will look like:
```php
class UploadListener
{
    public function handle($event)
    {
        $method = 'on'.class_basename($event);
        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $event);
        }
    }

    public function onImageWasUploaded(ImageWasUploaded $event)
    {
        $path = $event->path();
        //your code, for example resizing and cropping
    }
}
```

List of events:
 * Unisharp\Laravelfilemanager\Events\ImageWasUploaded

## Credits
 * All contibutors from GitHub. (issues / PR)
 * Special thanks to
   * [@taswler](https://github.com/tsawler) the original author.
   * [@olivervogel](https://github.com/olivervogel) for the awesome [image library](https://github.com/Intervention/image)
   * [@welcoMattic](https://github.com/welcoMattic) providing fr translations and lots of bugfixes.
   * [@fraterblack](https://github.com/fraterblack) TinyMCE 4 support and pt-BR translations.
   * [@1dot44mb](https://github.com/1dot44mb) tr translations.
   * [@Nikita240](https://github.com/Nikita240) fixing controller extending errors.
   * [@amin101](https://github.com/amin101) guide for independent use and fixes for url/directory error on Windows
   * [@nasirkhan](https://github.com/nasirkhan) bug fixes and alphanumeric filename check
   * All [@UniSharp](https://github.com/UniSharp) members
