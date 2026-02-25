#!/bin/bash
echo "🚀 Memulai Otomatisasi..."

# Tarik kodingan terbaru (jika pakai Git)
# git pull origin main

# Optimasi Laravel
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimasi Aset (Vite)
npm install
npm run build

# Perbaiki Izin Folder (agar tidak error 403 lagi)
sudo chown -R tunamayo678:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "✅ Server sudah optimal & kencang!"
