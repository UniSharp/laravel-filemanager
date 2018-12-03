## Upgrade instructions

  1. Please backup your own `config/lfm.php` before upgrading.

  1. Run commands:

      ```bash
      composer update unisharp/laravel-filemanager

      php artisan vendor:publish --tag=lfm_view --force
      php artisan vendor:publish --tag=lfm_public --force
      php artisan vendor:publish --tag=lfm_config --force

      php artisan route:clear
      php artisan config:clear
      ```

  1. Clear browser cache if page is broken after upgrading.

## Errors with namespace
We have changed namespace from `Unisharp` to `UniSharp`, and change the first character of every namespace into capital.

If you are updating this package and encounter any errors like `Class not found`, please remove this package entirely and reinstall again.
