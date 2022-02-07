#!/bin/bash
#crear carpeta de repositorio de documentos

mkdir storage/app/public/files
chown www-data:www-data storage/app/public/files

# Change access rights for the Laravel folders
# in order to make Laravel able to access
# cache and logs folder.
chgrp -R www-data storage bootstrap/cache && \
    chown -R www-data storage bootstrap/cache && \
    chmod -R ug+rwx storage bootstrap/cache
# Create log file for Laravel and give it write access
# www-data is a standard apache user that must have an
# access to the folder structure
touch storage/logs/laravel.log && chmod 775 storage/logs/laravel.log && chown www-data storage/logs/laravel.log
composer install && php artisan key:generate && npm install
