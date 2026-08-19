#/bin/bash
/usr/local/bin/composer dump-autoload
php8.3 artisan config:cache
php8.3 artisan route:clear
php8.3 artisan view:clear
php8.3 artisan cache:clear
