self.addEventListener('fetch', function (event) {
    // Biarkan kosong dulu untuk tahap awal (Simple PWA)
});
const CACHE_NAME = 'meter-spot-v1';
const urlsToCache = [
    '/',                  // Halaman utama Laravel
    '/css/app.css',       // Ganti dengan path CSS-mu
    '/js/app.js',         // Ganti dengan path JS-mu
    '/manifest.json',
    '/icons/icon-192x192.png'
];

// 1. Install Service Worker & Simpan Cache
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Membuka cache...');
                return cache.addAll(urlsToCache);
            })
    );
});

// 2. Logika saat Offline
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Jika ada di cache, pakai itu. Jika tidak, ambil dari internet
                return response || fetch(event.request).catch(() => {
                    // Jika benar-benar offline & tidak ada di cache, arahkan ke '/'
                    return caches.match('/');
                });
            })
    );
});