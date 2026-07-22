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

# Crear .env si no existe (necesario para artisan commands)
if [ ! -f .env ]; then
    echo "📝 Creando .env desde variables de entorno..."
    cat > .env <<EOF
APP_NAME=${APP_NAME:-Laravel}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-recetario}
DB_USERNAME=${DB_USERNAME:-recetario}
DB_PASSWORD=${DB_PASSWORD:-recetario_secret}
LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=${LOG_LEVEL:-warning}
EOF
    echo "✓ .env creado"
fi

# Establecer permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Cachear configuraciones primero (necesario para que use MySQL)
php artisan config:cache

# Ejecutar migraciones y seeders
php artisan migrate --force
php artisan db:seed --force --class=ExistingRecipesSeeder

# Cachear rutas y vistas
php artisan route:cache 2>/dev/null || true
php artisan view:cache

# Ejecutar el comando original (Apache)
exec "$@"
