const CACHE_NAME = 'amikomeventhub-v1';

// Aset statis yang di-cache dari awal (dipakai di banyak halaman, aman
// untuk disimpan lama). SENGAJA TIDAK meng-cache halaman HTML dinamis
// (seperti /, /event/1, dst) di sini - supaya data event/harga/stok yang
// sering berubah tidak "nyangkut" jadi basi di cache.
const STATIC_ASSETS = [
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/offline.html',
];

// --- INSTALL: simpan aset statis ke cache ---
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// --- ACTIVATE: bersihkan cache versi lama kalau ada ---
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

// --- FETCH: strategi beda untuk navigasi halaman vs aset statis ---
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Untuk navigasi antar halaman (buka URL baru): coba network dulu
    // (supaya selalu dapat data terbaru - harga, stok, dll), baru kalau
    // gagal (misal tidak ada internet), tampilkan halaman offline.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    // Untuk aset statis (CSS, JS, gambar, font, dll): coba cache dulu
    // (supaya loading lebih cepat), baru fallback ke network kalau
    // belum ada di cache.
    event.respondWith(
        caches.match(request).then((cached) => {
            return cached || fetch(request).then((response) => {
                // Simpan salinan ke cache untuk dipakai lagi nanti
                if (response.ok && request.method === 'GET') {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                }
                return response;
            });
        }).catch(() => {
            // Kalau benar-benar gagal semua (offline + belum ke-cache),
            // tidak ada yang bisa ditampilkan - biarkan error network biasa.
        })
    );
});