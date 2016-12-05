## Documents

  1. [Installation](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/installation.md)
  1. [Intergration](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/integration.md)
  1. [Config](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/config.md)
  1. [Customization](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/customization.md)
  1. [Events](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/events.md)
  1. [Upgrade](https://github.com/UniSharp/laravel-filemanager/blob/master/doc/upgrade.md)

## Upgrade guide
  * `composer update unisharp/laravel-filemanager`
  * `php artisan vendor:publish --tag=lfm_view --force`
  * `php artisan vendor:publish --tag=lfm_config --force` (IMPORTANT: please backup your own `config/lfm.php` first)

