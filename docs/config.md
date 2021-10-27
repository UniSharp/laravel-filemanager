**The config is in `config/lfm.php`.**

## Routing:

| Key                  | Type    | Description                                                                                                  |
|----------------------|---------|--------------------------------------------------------------------------------------------------------------|
| use\_package\_routes | boolean | Use routes from package or not. If false, you will need to define routes to all controllers of this package. |
| middlewares          | array   | Middlewares to be applied to default routes. For laravel 5.1 and before, remove 'web' from the array.        |
| url_prefix           | string  | The url prefix to this package. Change it if necessary.                                                      |


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

## Working Directory:

| Key                  | Type   | Description                                                                                                                                                                     |
|----------------------|--------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| base_directory       | string | Which folder to store files in project, fill in 'public', 'resources', 'storage' and so on. Does not support path relative path like `../public_html` or `public/upload/user/`. |
| images\_folder\_name | string | Does not support path relative path like `../public_html` or `public/upload/user/`.                                                                                             |
| files\_folder\_name  | string | Does not support path relative path like `../public_html` or `public/upload/user/`.                                                                                             |
| shared\_folder\_name | string | Does not support path relative path like `../public_html` or `public/upload/user/`.                                                                                             |
| thumb\_folder\_name  | string | Does not support path relative path like `../public_html` or `public/upload/user/`.                                                                                             |


## Startup Views:

| Key                   | Type   | Description                                                     |
|-----------------------|--------|-----------------------------------------------------------------|
| images\_startup\_view | string | The default display type for images. Supported: "grid", "list". |
| files\_startup\_view  | string | The default display type for files. Supported: "grid", "list".  |


## Upload / Validation:

| Key                        | Type    | Description                                                               |
|----------------------------|---------|---------------------------------------------------------------------------|
| disk (Alpha version only)  | string  | Correspond to `disks` section in `config/filesystems.php`.                |
| rename_file                | string  | If true, the uploaded file will be renamed to uniqid() + file extension.  |
| alphanumeric_filename      | string  | If  true, non-alphanumeric file name will be replaced with `_`.           |
| alphanumeric_directory     | boolean | If true, non-alphanumeric folder name will be rejected.                   |
| should\_validate\_size     | boolean | If true, the size of uploading file will be verified.                     |
| max\_image\_size           | int     | Specify max size of uploading image.                                      |
| max\_file\_size            | int     | Specify max size of uploading file.                                       |
| should\_validate\_mime     | boolean | If true, the mime type of uploading file will be verified.                |
| valid\_image\_mimetypes    | array   | Array of mime types. Available since v1.3.0 .                             |
| should\_create\_thumbnails | boolean | If true, thumbnails will be created for faster loading.                   |
| raster\_mimetypes          | array   | Array of mime types. Thumbnails will be created only for these mimetypes. |
| create\_folder\_mode       | int     | Permission setting for folders created by this package.                   |
| create\_file\_mode         | int     | Permission setting for files uploaded to this package.                    |
| should\_change\_file\_mode | boolean | If true, it will attempt to chmod the file after upload                   |
| valid\_file\_mimetypes     | array   | Array of mime types. Available since v1.3.0 .                             |

##### Appendix:

  * [full mime types list](http://docs.w3cub.com/http/basics_of_http/mime_types/complete_list_of_mime_types/)
  * [Laravel File Storage](https://laravel.com/docs/master/filesystem)


## Thumbnail dimensions:

| Key                | Type   | Description                                      |
|--------------------|--------|--------------------------------------------------|
| thumb\_img\_width  | string | Width of thumbnail made when image is uploaded.  |
| thumb\_img\_height | string | Height of thumbnail made when image is uploaded. |


## File Extension Information

| Key               | Type  | Description                                 |
|-------------------|-------|---------------------------------------------|
| file\_type\_array | array | Map file extension with display names.      |
| file\_icon\_array | array | Map file extension with icons(font-awsome). |


## php.ini override

| Key                 | Type             | Description                                                                                                                       |
|---------------------|------------------|-----------------------------------------------------------------------------------------------------------------------------------|
| php\_ini\_overrides | array or boolean | These values override your php.ini settings before uploading files. Set these to false to ingnore and apply your php.ini settings |

### Caveats

The php\_ini\_overrides are applied on every request the filemanager does and are reset once the script has finished executing.
This has one drawback: any ini settings that you might want to change that apply to the request itself will not work.

For example, overriding these settings will not work:
* upload\_max\_filesize
* post\_max\_size

**Why this is expected behaviour:**
upload\_max\_filesize and post\_max\_size will get set but uploaded files are already passed to your PHP script before the settings are changed.
