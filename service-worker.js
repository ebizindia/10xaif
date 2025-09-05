// Define a cache name for the files
const CACHE_NAME = 'static-cache-v1';

// Files to cache
const FILES_TO_CACHE = [
  'css/bootstrap.min.css',
  'js/bootstrap.min.js'
];

self.addEventListener('install', (event) => {
  console.log('Service worker installed');

  // Wait until the cache is opened and files are added
  event.waitUntil(
      caches.open(CACHE_NAME)
          .then((cache) => {
            console.log('Cache opened');
            return cache.addAll(FILES_TO_CACHE);
          })
  );

  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  console.log('Service worker activated');

  // Clean up old caches if needed
  event.waitUntil(
      caches.keys().then((cacheNames) => {
        return Promise.all(
            cacheNames.map((cacheName) => {
              if (cacheName !== CACHE_NAME) {
                console.log('Deleting old cache:', cacheName);
                return caches.delete(cacheName);
              }
            })
        );
      })
  );
});

self.addEventListener('fetch', (event) => {
  // Check if the request is for one of our cached files
  if (FILES_TO_CACHE.includes(new URL(event.request.url).pathname)) {
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
              // Return cached response if found
              if (response) {
                console.log('Serving from cache:', event.request.url);
                return response;
              }

              // Otherwise fetch from network and cache the response
              console.log('Fetching from network:', event.request.url);
              return fetch(event.request)
                  .then((response) => {
                    // Clone the response as it can only be consumed once
                    const clonedResponse = response.clone();

                    caches.open(CACHE_NAME)
                        .then((cache) => {
                          cache.put(event.request, clonedResponse);
                        });

                    return response;
                  });
            })
    );
  } else {
    // For other requests, just fetch from the network
    event.respondWith(fetch(event.request));
  }
});