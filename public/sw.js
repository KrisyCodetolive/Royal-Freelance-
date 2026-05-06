const CACHE_NAME = 'royal-leadmagnet-v3';

const PRE_CACHE = [
    '/commercial',
    '/commercial/leads',
    '/commercial/alerts',
    '/commercial/tunnels',
    '/commercial/profile',
];

// Installation — pré-cache les pages clés
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(PRE_CACHE))
    );
    self.skipWaiting();
});

// Activation — supprime les anciens caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// Fetch — Cache First (stale-while-revalidate) pour les pages HTML
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;
    if (!event.request.url.startsWith(self.location.origin)) return;

    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/livewire')) return;

    const isHTML = event.request.headers.get('accept')?.includes('text/html');

    if (isHTML) {
        event.respondWith(
            caches.match(event.request).then(cached => {
                // Revalide le cache en arrière-plan
                const fetchPromise = fetch(event.request).then(response => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                    }
                    return response;
                }).catch(() => null);

                // Retourne le cache immédiatement si dispo, sinon attend le réseau
                return cached || fetchPromise || caches.match('/commercial');
            })
        );
    } else {
        // Assets (JS, CSS, images) : cache first sans revalidation
        event.respondWith(
            caches.match(event.request).then(cached => {
                return cached || fetch(event.request).then(response => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                    }
                    return response;
                });
            })
        );
    }
});

// Push — affiche la notification reçue du serveur
self.addEventListener('push', event => {
    if (!event.data) return;

    let data;
    try {
        data = event.data.json();
    } catch {
        data = { title: 'Royal LeadMagnet', body: event.data.text() };
    }

    const title = data.title || 'Royal LeadMagnet';
    const options = {
        body: data.body || '',
        icon: '/assets/icons/icon-192.png',
        badge: '/assets/icons/icon-192.png',
        tag: data.tag || 'royal-alert',
        renotify: true,
        data: { url: data.url || '/commercial/alerts' },
        actions: [
            { action: 'open', title: 'Voir' },
            { action: 'close', title: 'Fermer' },
        ],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// Clic sur la notification — ouvre l'URL cible
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'close') return;

    const targetUrl = event.notification.data?.url || '/commercial/alerts';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            for (const client of clientList) {
                if (client.url.includes('/commercial') && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            return clients.openWindow(targetUrl);
        })
    );
});
