#############################
# 0) Shared PHP base (install extensions once)
#############################
FROM serversideup/php:8.4-fpm-nginx-alpine AS php-base

USER root
WORKDIR /var/www/html

RUN install-php-extensions gd

#############################
# 1) PHP dependencies (prod)
#############################
FROM php-base AS vendor

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY artisan ./

RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN --mount=type=cache,target=/tmp/composer-cache \
    composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --classmap-authoritative \
    --no-scripts

#############################
# 2) Frontend assets + SSR bundle
#############################
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci --no-audit --no-fund

COPY resources ./resources
COPY public ./public
COPY bootstrap ./bootstrap
COPY tsconfig.json ./
COPY vite.config.* ./
COPY --from=vendor /app/vendor/tightenco/ziggy ./vendor/tightenco/ziggy

RUN npm run build:ssr

#############################
# 2b) Node runtime deps for SSR
#############################
FROM node:20-alpine AS node-runtime-deps

WORKDIR /app

COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci --omit=dev --no-audit --no-fund

#############################
# 3) SSR runtime (Node required)
#############################
FROM php-base AS ssr-runtime

USER root
WORKDIR /var/www/html

RUN apk add --no-cache nodejs \
    && rm -rf /var/cache/apk/*

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY --from=frontend /app/bootstrap/ssr ./bootstrap/ssr
COPY --from=node-runtime-deps /app/node_modules ./node_modules
COPY package.json ./

RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 13714

CMD ["php", "artisan", "inertia:start-ssr", "--runtime=node"]

#############################
# 4) App runtime (no Node, no Composer)
#############################
FROM php-base AS app-runtime

USER root
WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY --from=frontend /app/bootstrap/ssr ./bootstrap/ssr

RUN mkdir -p /var/www/html/storage/app/public /var/www/html/public \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chown -h www-data:www-data /var/www/html/public/storage \
    && (rm -f /usr/local/bin/composer /usr/bin/composer 2>/dev/null || true)

USER www-data

EXPOSE 8080