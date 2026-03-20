#/bin/bash
# exit when any command fails
set -e

# keep track of the last executed command
trap 'last_command=$current_command; current_command=$BASH_COMMAND' DEBUG
# echo an error message before exiting
trap 'echo "\"${last_command}\" command filed with exit code $?."' EXIT

#Ejecutar
composer install
php artisan key:generate
php artisan migrate
composer dump-autoload
php artisan db:seed
php artisan storage:link
# Cambiar dueño y permisos de la carpeta build dentro de public
sudo chown -R www-data:www-data ../public/build
sudo chmod -R 755 ../public/build

curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
nvm install 20
nvm use 20
# Borramos la carpeta node_modules por las dudas para evitar conflictos de versiones previas
rm -rf node_modules package-lock.json

# Instalamos y compilamos
npm install
npm run build
