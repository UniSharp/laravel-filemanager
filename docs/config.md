**The config is in `config/lfm.php`.**

## Routing:

### use\_package\_routes

* type: `boolean`
* default: `true`

Use default routes or not. You will need to define routes to all controllers of this package if this is set to `false`.


## Multi-User Mode:

### allow\_private\_folder

* type: `boolean`
* default: `true`

Only the owner(each signed-in user) of the private can upload and manage files within. Set to `false` to turn this feature off.

### private\_folder\_name

* type: `string`
* default: user id

Privates folders for each user will be named by this config. Default to user id.

To change the behavior:

1. run `php artisan publish tag="lfm_handler"`
2. rewrite `userField` function in App\Handler\ConfigHandler class
3. set value of this config to App\Handler\ConfigHandler::class

### allow\_shared\_folder

* type: `boolean`
* default: `true`

### shared\_folder\_name

* type: `string`
* default: `"shares"`

Flexible way to customize client folders accessibility.

If you want to customize client folders:

1. run `php artisan publish tag="lfm_handler"`
2. rewrite `userField` function in `App\Handler\ConfigHandler` class
3. set value of this config to `App\Handler\ConfigHandler::class`

All users can upload and manage files within shared folders. Set to `false` to turn this feature off.


## Folder Categories

### folder\_categories

* type: `array` (nested)
* default: 

```
'folder_categories' => [
    'file'  => [
        'folder_name'  => 'files',
        'startup_view' => 'list',
        'max_size'     => 50000, // size in KB
        'valid_mime'   => [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'text/plain',
        ],
    ],
    'image' => [
        'folder_name'  => 'photos',
        'startup_view' => 'grid',
        'max_size'     => 50000, // size in KB
        'valid_mime'   => [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
        ],
    ],
],
```

The default config creates two folder categories, `file` and `image`, each operates independently. Files uploaded by users will be placed under one of these folder categories, depend on which is configured with your WYSIWYG editor or stand-alone upload button.

Detail options are explained here: 

* `folder_name`: The folder name of the category. For example, if `folder_name` is set to `files2` then: 
  * directory path of the private folder will be: `/<path-to-laravel>/storage/app/public/files2/<user-id>/`
  * directory path of the shared folder will be: `/<path-to-laravel>/storage/app/public/files2/shares/`
* `startup_view`: The default display mode. Available options: `list` & `grid`.
* `max_size`: The maximum size(in KB) of of a single file to be uploaded.
* `valid_mime`: Only files with mime types listed here are allowed to be uploaded. See [full mime types list](http://docs.w3cub.com/http/basics_of_http/mime_types/complete_list_of_mime_types/).

## Pagination:

### paginator

* type: `array`
* default: 

```
'paginator' => [
    'perPage' => 30,
],
```


## Upload / Validation:

### disk

* type: `string`
* default: `public`

Disk name of Laravel File System. All files are placed in here. Choose one of the `disks` section in `config/filesystems.php`.

### temporary\_url\_duration

* type: `int`
* default: `30`

Duration of AWS S3 temporary urls, in minutes.

### s3\_acls\_disabled

* type: `boolean`
* default: `false`

Enable to support AWS S3 “Bucket owner enforced” (ACL-disabled) buckets.

### rename\_file

* type: `boolean`
* default: `false`

If set to `true`, the uploaded file will be renamed using `uniqid()`.

### alphanumeric\_filename

* type: `boolean`
* default: `false`

If set to `true`, non-alphanumeric file name will be replaced with `_`.

### alphanumeric\_directory

* type: `boolean`
* default: `false`

If set to `true`, non-alphanumeric folder name will be rejected.

### should\_validate\_size

* type: `boolean`
* default: `false`

If set to `true`, the size of uploading file will be verified.

### should\_validate\_mime

* type: `boolean`
* default: `true`

If set to `true`, the mime type of uploading file will be verified.

### over\_write\_on_duplicate

* type: `int`
* default: `false`

Define behavior on files with identical name. Setting it to `true` cause old file replace with new one. Setting it to `false` show `error-file-exist` error and abort the upload process.

## Image Optimization

### optimize\_uploaded\_images

* type: `array`
* default:

```
'optimize_uploaded_images' => [
    'enabled' => false,
    'format' => null,
    'quality' => 85,
    'max_width' => null,
    'max_height' => null,
    'progressive' => true,
    'keep_original_when_larger' => true,
    'mimetypes' => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/webp',
        'image/avif',
        'image/bmp',
        'image/tiff',
        'image/jp2',
        'image/heic',
    ],
],
```

When `enabled` is `true`, supported uploaded images are re-encoded after the original upload succeeds.

By default `format` is `null`, so the original image format is kept. Set `format` to an output format such as `jpg`, `png`, `webp`, or `avif` to convert supported uploads. Converted files use the matching extension, for example `photo.jpg` becomes `photo.webp` when `format` is `webp`.

Driver-dependent formats such as `gif`, `bmp`, `tiff`, `jp2`, and `heic` are also accepted. If the configured GD or Imagick driver cannot encode the selected format, the original upload is kept.

When the GD driver is used, WebP output requires PHP's `imagewebp()` function and AVIF output requires PHP's `imageavif()` function. If the installed GD extension does not provide the required encoder, the upload is kept unchanged and the reason is logged.

GIF uploads are not included in the default `mimetypes` list because re-encoding animated GIFs can change animation behavior depending on the driver. Add `image/gif` explicitly if that trade-off is acceptable for your application.

`max_width` and `max_height` can be used to scale large uploads down while keeping the aspect ratio. Set both to `null` to keep the uploaded dimensions.

`quality` is used for lossy output formats only: `jpg`, `webp`, and `avif`. The value is clamped to the `0..100` range, and non-numeric values fall back to `85`.

`progressive` only affects JPEG output. Other formats ignore it.

If optimization fails, the original upload is kept. If `keep_original_when_larger` is enabled, the optimized image only replaces the upload when it is smaller than the original. If conversion would overwrite an existing filename, the original upload is kept.

Example verification scenarios:

1. Keep JPEG format, resize large uploads, and use a lower quality setting:

```
'optimize_uploaded_images' => [
    'enabled' => true,
    'format' => null,
    'quality' => 75,
    'max_width' => 1600,
    'max_height' => 1600,
    'progressive' => true,
],
```

Upload a large `.jpg`. Expected result: the stored file stays `.jpg`, dimensions are scaled down to fit within `1600x1600`, and the JPEG is re-encoded with quality `75`.

2. Convert screenshots to WebP:

```
'optimize_uploaded_images' => [
    'enabled' => true,
    'format' => 'webp',
    'quality' => 80,
    'mimetypes' => ['image/png', 'image/jpeg', 'image/pjpeg'],
],
```

Upload a `.png` or `.jpg`. Expected result: the stored file becomes `.webp`. `quality` is applied to the WebP output.

3. Re-encode PNG without format conversion:

```
'optimize_uploaded_images' => [
    'enabled' => true,
    'format' => 'png',
    'quality' => 30,
],
```

Upload a `.png`. Expected result: the stored file stays `.png`. The `quality` setting does not change the code path here, because PNG uses the driver default encode options in this implementation.

## Thumbnail

### should\_create\_thumbnails

* type: `boolean`
* default: `true`

If set to `true`, thumbnails will be created for faster loading.

### thumb\_folder\_name

* type: `string`
* default: `thumbs`

Folder name to place thumbnails.

### raster\_mimetypes

* type: `array`
* default:

```
'raster_mimetypes' => [
    'image/jpeg',
    'image/pjpeg',
    'image/png',
],
```

Create thumbnails automatically only for listed types. See [full mime types list](http://docs.w3cub.com/http/basics_of_http/mime_types/complete_list_of_mime_types/).

### thumb_img_width

* type: `int`
* default: `200`

Thumbnail images width (in px).

### thumb_img_height

* type: `int`
* default: `200`

Thumbnail images height (in px).

Create thumbnails automatically only for listed types.



## File Extension Information

### file\_type\_array

* type: `array`
* default:

```
'file_type_array' => [
    'pdf'  => 'Adobe Acrobat',
    'doc'  => 'Microsoft Word',
    'docx' => 'Microsoft Word',
    'xls'  => 'Microsoft Excel',
    'xlsx' => 'Microsoft Excel',
    'zip'  => 'Archive',
    'gif'  => 'GIF Image',
    'jpg'  => 'JPEG Image',
    'jpeg' => 'JPEG Image',
    'png'  => 'PNG Image',
    'ppt'  => 'Microsoft PowerPoint',
    'pptx' => 'Microsoft PowerPoint',
],
```

Gives description for listed file extensions.

## php.ini override

### php\_ini\_overrides

* type: `array` or `boolean`
* default:


```
'php_ini_overrides' => [
    'memory_limit' => '256M',
],
```

These values override your php.ini settings before uploading files. Set these to false to ingnore and apply your php.ini settings

⚠️ **Caveats**

The php\_ini\_overrides are applied on every request the filemanager does and are reset once the script has finished executing.
This has one drawback: any ini settings that you might want to change that apply to the request itself will not work.

For example, overriding these settings will not work:
* upload\_max\_filesize
* post\_max\_size

**Why this is expected behaviour:**
upload\_max\_filesize and post\_max\_size will get set but uploaded files are already passed to your PHP script before the settings are changed.
