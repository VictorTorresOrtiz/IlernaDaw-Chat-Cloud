const staticCacheName = 'IDAW-cache';
const dynamicCacheName = 'runtimeCache';

// Pre Carga de Assets
const precacheAssets = [
    '../OnlineLauncher',
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

// Evento SERVER DOWN
self.addEventListener('fetch', function (event) {
    event.respondWith(
        caches.match(event.request).then(cacheRes => {
            return cacheRes || fetch(event.request).then(response => {
                return caches.open(dynamicCacheName).then(function (cache) {
                    cache.put(event.request, response.clone());
                    return response;
                })
            });
        }).catch(function() {
            return caches.match('INTRODUCIR PAGINA CAIDA SERVER');
          })
    );
});