#/bin/bash
composer dump-autoload
php artisan config:cache
php artisan route:clear
php artisan view:clear
php artisan cache:clear
