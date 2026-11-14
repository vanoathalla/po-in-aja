#!/bin/sh
set -e

echo "🚀 [PO-in Aja] Booting container for Koyeb / Render Free Tier..."

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate App Key if missing
if [ -z "$APP_KEY" ]; then
    echo "⚠️ APP_KEY is empty. Generating temporary application key..."
    php artisan key:generate --force || true
fi

# Link local storage for fallbacks
php artisan storage:link --force || true

# Run database migrations against Supabase PostgreSQL
echo "📦 Running Supabase Database Migrations..."
php artisan migrate --force --isolated || {
    echo "❌ Migration failed or Supabase connection issue. Retrying in 3 seconds..."
    sleep 3
    php artisan migrate --force
}

# Production Cache Optimizations
echo "⚡ Caching Configurations, Routes, and Views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize FilamentPHP v3 Components & Icons
echo "🎨 Optimizing FilamentPHP v3 Panel..."
php artisan filament:optimize || true

echo "✅ App initialization complete. Starting Nginx & PHP-FPM via Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
