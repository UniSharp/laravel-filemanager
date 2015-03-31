# laravel-filemanager

A file upload/editor intended for use with [Laravel 5](http://www.laravel.com/ "Title") and [CKEditor](http://ckeditor.com/).

## Rationale

There are other packages out there that offer much the same functionality, such as [KCFinder](http://kcfinder.sunhater.com/),
but I found that integration with Laravel was a bit problematic, particularly when it comes to handling sessions
and security.

This package is written specifically for Laravel 5, and will integrate seamlessly.

## Requirements

1. This package only supports Laravel 5.x
1. Requires `"intervention/image": "2.*",`
1. Requires `"francodacosta/phmagick": "0.4.*@dev"`
1. Requires PHP 5.5 or later

## Installation

Installation is done through composer and packagist:

`composer require tsawler\laravel-filemanager`

After updating composer, add the ServiceProvider to the providers array in config/app.php:

`'Tsawler\Laravelfilemanager\LaravelFilemanagerServiceProvider',`
