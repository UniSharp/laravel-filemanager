## Documents

  1. [Installation](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/installation.md)
  1. [Intergration](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/integration.md)
  1. [Config](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/config.md)
  1. [Customization](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/customization.md)
  1. [Events](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/events.md)
  1. [Upgrade](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/upgrade.md)

## Config
    
In `config/lfm.php` :

```php
    'rename_file'           => true,
    // true : files will be renamed as uniqid
    // false : files will remain original names

    // true : filter filename characters which are not alphanumeric, and replace them with '_'
    'alphanumeric_filename' => true,

    'use_package_routes'    => true,
    // set this to false to customize route for file manager

    'middlewares'           => ['auth'],
    // determine middlewares that apply to all file manager routes
    // NOTE: for laravel 5.2, please use ['web', 'auth']

    'allow_multi_user'      => true,
    // true : user can upload files to shared folder and their own folder
    // false : all files are put together in shared folder

    'user_field'            => 'id',
    // determine which column of users table will be used as user's folder name

    'shared_folder_name'    => 'shares',
    // the name of shared folder

    'thumb_folder_name'     => 'thumbs',
    // the name of thumb folder

    'images_dir'            => 'public/photos/',
    'images_url'            => '/photos/',
    // path and url of images

    'files_dir'             => 'public/files/',
    'files_url'             => '/files/',
    // path and url of files


    // valid image mimetypes
    'valid_image_mimetypes' => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/gif'
    ],


    // valid file mimetypes (only when '/laravel-filemanager?type=Files')
    'valid_file_mimetypes' => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'text/plain'
    ],
```
