## Documents

  1. [Installation](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/installation.md)
  1. [Integration](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/integration.md)
  1. [Config](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/config.md)
  1. [Customization](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/customization.md)
  1. [Events](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/events.md)
  1. [Upgrade](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/upgrade.md)

## Upgrade instructions

  1. Please backup your own `config/lfm.php` before upgrading.
  1. Run commands:

    ```bash
    composer update unisharp/laravel-filemanager
    php artisan vendor:publish --tag=lfm_view --force
    php artisan vendor:publish --tag=lfm_public --force
    php artisan vendor:publish --tag=lfm_config --force
    ```
  1. Clear browser cache if page is broken after upgrading.

