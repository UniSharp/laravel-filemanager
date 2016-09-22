# Laravel Filemanager

[![Latest Stable Version](https://poser.pugx.org/jayked/laravel-filemanager/version?format=flat)](https://packagist.org/packages/jayked/laravel-filemanager)
[![Total Downloads](https://poser.pugx.org/jayked/laravel-filemanager/downloads?format=flat)](https://packagist.org/packages/jayked/laravel-filemanager)
[![License](https://poser.pugx.org/jayked/laravel-filemanager/license?format=flat)](https://packagist.org/packages/jayked/laravel-filemanager)

[Read the docs live](http://jayked.github.io/laravel-filemanager)

A files and images management user interface with file uploading support. (Works well with CKEditor and TinyMCE)

PR is welcome!

## Overview

 * The project was forked from [unisharp/laravel-filemanager](http://packalyst.com/packages/package/unisharp/laravel-filemanager)
 * Customizable routes and middlewares
 * Supported locales :
    - en
    - fr
    - pt-BR
    - tr
    - zh-CN
    - zh-TW
    - nl
 * Supports public and private folders for multi users
 * Supports multi-level folders
 * Supports using independently(see integration doc)

## Documents

  1. [Installation](https://github.com/Jayked/laravel-filemanager/blob/master/doc/installation.md)
  1. [Intergration](https://github.com/Jayked/laravel-filemanager/blob/master/doc/integration.md)
  1. [Config](https://github.com/Jayked/laravel-filemanager/blob/master/doc/config.md)
  1. [Customization](https://github.com/Jayked/laravel-filemanager/blob/master/doc/customization.md)
  
## Compatibility

The `jayked/laravel-filemanager` is compatible with the following versions of Laravel:
  - 4.2
  - 5.0
  - 5.1
  - 5.2
  - 5.3

## Upgrade guide
  * `composer update jayked/laravel-filemanager`
  * `php artisan vendor:publish --tag=lfm_view --force`
  * `php artisan vendor:publish --tag=lfm_config --force`(remember to keep your previous settings in `config/lfm.php`)

## Screenshots

  * List view :

![FileManager screenshot 1](http://jayked.github.io/laravel-filemanager/filemanager2.png)

  * Grid view :

![FileManager screenshot 2](http://jayked.github.io/laravel-filemanager/filemanager1.png)

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
   * [@Jayked](https://github.com/Jayked) with small fixes and new options
