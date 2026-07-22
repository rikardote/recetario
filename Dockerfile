FROM php:8.4-apache

# Instalar extensiones de PHP y utilidades necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libmariadb-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    gd \
    zip \
    bcmath \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite de Apache (necesario para Laravel)
RUN a2enmod rewrite

# Configurar DocumentRoot de Apache a /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Permitir .htaccess (necesario para las rutas de Laravel)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copiar composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar Node.js y npm desde una imagen oficial
COPY --from=node:22 /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node:22 /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de dependencias primero (para aprovechar caché de Docker)
COPY composer.json composer.lock package.json package-lock.json vite.config.js ./
COPY .npmrc ./
COPY resources/ resources/
COPY public/ public/

# Instalar dependencias de Composer (solo producción, sin scripts porque artisan no existe aún)
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts

# Instalar dependencias npm y construir assets
RUN npm ci --ignore-scripts && npm run build && rm -rf node_modules

# Copiar el resto del código de la aplicación
COPY . .

# Ejecutar scripts de Composer que necesitan artisan (post-autoload-dump, package:discover)
RUN composer run post-autoload-dump --no-dev 2>/dev/null || true

# Dar permisos al entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Crear directorio storage con permisos correctos
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Exponer puerto 80
EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
