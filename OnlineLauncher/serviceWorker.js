const staticCacheName = 'IDAW-cache';
const dynamicCacheName = 'runtimeCache';

// Pre Carga de Assets
const precacheAssets = [
    './',
    './pwa.js',
    'index.php',
    'index.html',
    
    
];

// Instalar Evento
self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(staticCacheName).then(function (cache) {
            return cache.addAll(precacheAssets);
        })
    );
});

// Activatar Evento
self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(keys
                .filter(key => key !== staticCacheName && key !== dynamicCacheName)
                .map(key => caches.delete(key))
            );
        })
    );
});


//Control fetch
self.addEventListener('fetch', function(event) {
    console.log('Fetching some data');
    console.log(event.request.url);
    event.respondWith(
    caches.match(event.request).then(function(response) {
    console.log('File is on the cache! Hooray! Let\'s take it from there!');
    return response || fetch(event.request);
    })
    );
    });

