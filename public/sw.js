/**
 * Service Worker for Sodai Dorkar (সদাই দরকার)
 * Handles Native Push Notifications, In-App Alerts, Background Caching
 */

const CACHE_NAME = 'sodai-dorkar-cache-v1';

// Install Event
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

// Push Event: Triggered when Web Push / Push API message is received
self.addEventListener('push', (event) => {
    let data = {};
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = { title: 'সদাই দরকার', message: event.data.text() };
        }
    }

    const title = data.title || '🔥 সদাই দরকার - নতুন অফার!';
    const options = {
        body: data.message || data.body || 'আপনার জন্য রয়েছে আকর্ষণীয় ডিসকাউন্ট!',
        icon: data.icon || '/sodai-dorkar/public/images/logo.png',
        badge: '/sodai-dorkar/public/images/logo.png',
        image: data.image_url || data.image || undefined,
        data: {
            url: data.action_url || data.url || '/sodai-dorkar/public/shop',
            id: data.id || null
        },
        vibrate: [200, 100, 200],
        tag: 'sodai-promo-' + (data.id || Date.now()),
        renotify: true
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification Click Event: Navigate user directly to promo/target page
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url)
        ? event.notification.data.url
        : '/sodai-dorkar/public/shop';

    const notifId = event.notification.data ? event.notification.data.id : null;
    if (notifId) {
        fetch('/sodai-dorkar/public/api/notifications/track-click?id=' + encodeURIComponent(notifId), {
            method: 'POST'
        }).catch(() => {});
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // If already open in tab, focus it
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if ('focus' in client && client.url.includes('/sodai-dorkar/public')) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            // Otherwise open a new window
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
