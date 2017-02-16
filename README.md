# Laravel Filemanager
[![Latest Stable Version](https://poser.pugx.org/unisharp/laravel-filemanager/v/stable)](https://packagist.org/packages/unisharp/laravel-filemanager)
[![Total Downloads](https://poser.pugx.org/unisharp/laravel-filemanager/downloads)](https://packagist.org/packages/unisharp/laravel-filemanager)
[![License](https://poser.pugx.org/unisharp/laravel-filemanager/license)](https://packagist.org/packages/unisharp/laravel-filemanager)

To preview all features, clone [Laravel Filemanager container](https://github.com/UniSharp/laravel-filemanager-example-5.3).

## Documents
  1. [Installation](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/installation.md)
  1. [Integration](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/integration.md)
  1. [Config](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/config.md)
  1. [Customization](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/customization.md)
  1. [Events](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/events.md)

## Features
 * CKEditor and TinyMCE integration
 * Standalone button
 * Uploading validation
 * Cropping and resizing of images
 * Public and private folders for multi users
 * Customizable routes, middlewares, views, and folder path
 * Supports two types : files and images. Each type works in different directory.
 * Supported locales : ar, bg, en, es, fa, fr, he, hu, nl, pt-BR, pt_PT, ro, ru, tr, zh-CN, zh-TW

PR is welcome!

## Upgrade guide
 * Please backup your own `config/lfm.php` before upgrading.
 * Run commands:

  ```bash
  composer update unisharp/laravel-filemanager
  php artisan vendor:publish --tag=lfm_view --force
  php artisan vendor:publish --tag=lfm_public --force
  php artisan vendor:publish --tag=lfm_config --force
  ```
 * Clear browser cache if page is broken after upgrading.

## Screenshots
> Standalone button :

![Standalone button demo](https://raw.githubusercontent.com/UniSharp/laravel-filemanager/gh_pages/images/lfm01.png)

> Grid view :

![Grid view demo](https://raw.githubusercontent.com/UniSharp/laravel-filemanager/gh_pages/images/lfm02.png)

> List view :

![List view demo](https://raw.githubusercontent.com/UniSharp/laravel-filemanager/gh_pages/images/lfm03.png)
  
## Credits
Special thanks to

 * [All contibutors](https://github.com/UniSharp/laravel-filemanager/graphs/contributors) from GitHub. (issues / PR)
 * [@taswler](https://github.com/tsawler) the original author.
 * [@olivervogel](https://github.com/olivervogel) for the awesome [image library](https://github.com/Intervention/image).
 * All [@UniSharp](https://github.com/UniSharp) members.
