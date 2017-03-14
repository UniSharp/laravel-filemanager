## Documents
  1. [Installation](installation)
  1. [Integration](integration)
  1. [Config](config)
  1. [Customization](customization)
  1. [Events](events)
  1. [Upgrade](upgrade)

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

