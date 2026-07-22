#!/bin/bash
set -e

# Esperar a que MySQL esté listo
echo "⏳ Esperando a MySQL..."
max_retries=30
counter=0
until php -r "new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-recetario}', '${DB_USERNAME:-recetario}', '${DB_PASSWORD:-recetario_secret}');" 2>/dev/null; do
    counter=$((counter + 1))
    if [ $counter -ge $max_retries ]; then
        echo "✗ MySQL no está disponible después de $max_retries intentos. Abortando."
        exit 1
    fi
    echo "  Intentando conexión... ($counter/$max_retries)"
    sleep 2
done
echo "✓ MySQL está listo"

# Establecer permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Cachear configuraciones antes de migrar (necesario para que use MySQL)
php artisan config:cache

# Ejecutar migraciones y seeders
php artisan migrate --force
php artisan db:seed --force --class=ExistingRecipesSeeder

# Cachear rutas y vistas
php artisan route:cache
php artisan view:cache

# Ejecutar el comando original (Apache)
exec "$@"
