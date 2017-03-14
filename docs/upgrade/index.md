## Documents
  1. [Installation](https://unisharp.github.io/laravel-filemanager/installation)
  1. [Integration](https://unisharp.github.io/laravel-filemanager/integration)
  1. [Config](https://unisharp.github.io/laravel-filemanager/config)
  1. [Customization](https://unisharp.github.io/laravel-filemanager/customization)
  1. [Events](https://unisharp.github.io/laravel-filemanager/events)
  1. [Upgrade](https://unisharp.github.io/laravel-filemanager/upgrade)

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
