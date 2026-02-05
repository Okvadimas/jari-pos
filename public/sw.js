/**
 * Jari POS Service Worker
 * Handles caching and offline functionality
 * Version: 2.0 - Enhanced for POS offline transactions
 */

const CACHE_NAME = 'jaripos-cache-v2';
const API_CACHE_NAME = 'jaripos-api-cache-v1';
const OFFLINE_URL = '/offline';

// Assets to cache immediately on install
const PRECACHE_ASSETS = [
    '/',
    '/dashboard',
    '/pos',
    '/offline',
    '/images/brand-logo.svg',
    '/images/brand-full-logo-side.png',
    '/images/product-sample.png',
    '/js/bundle.js',
    '/manifest.json'
];

// API endpoints to cache for offline use
const CACHEABLE_APIS = [
    '/pos/products',
    '/pos/categories',
    '/pos/vouchers'
];

// Install event - cache core assets
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing v2...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Pre-caching core assets');
                return cache.addAll(PRECACHE_ASSETS);
            })
            .then(() => {
                console.log('[Service Worker] Skip waiting');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('[Service Worker] Pre-cache failed:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    
    const currentCaches = [CACHE_NAME, API_CACHE_NAME];
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => !currentCaches.includes(cacheName))
                        .map((cacheName) => {
                            console.log('[Service Worker] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log('[Service Worker] Claiming clients');
                return self.clients.claim();
            })
    );
});

// Check if URL is a cacheable API endpoint
function isCacheableAPI(url) {
    return CACHEABLE_APIS.some(api => url.pathname.startsWith(api));
}

// Fetch event - Network first, fallback to cache strategy
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip external requests
    if (url.origin !== location.origin) {
        return;
    }

    // Skip auth-related requests
    if (url.pathname.startsWith('/logout') ||
        url.pathname.startsWith('/login')) {
        return;
    }

    // Handle cacheable API requests (products, categories, vouchers)
    if (isCacheableAPI(url)) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Clone and cache the response
                    const responseClone = response.clone();
                    caches.open(API_CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    // Offline - try to serve from cache
                    console.log('[Service Worker] Serving API from cache:', url.pathname);
                    return caches.match(request);
                })
        );
        return;
    }

    // For navigation requests (HTML pages including /pos)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Clone response and cache it
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    // Try to serve from cache
                    return caches.match(request)
                        .then((cachedResponse) => {
                            if (cachedResponse) {
                                return cachedResponse;
                            }
                            // If no cache, serve offline page
                            return caches.match(OFFLINE_URL);
                        });
                })
        );
        return;
    }

    // For static assets - Cache first, then network
    if (request.destination === 'style' || 
        request.destination === 'script' || 
        request.destination === 'image' ||
        request.destination === 'font') {
        event.respondWith(
            caches.match(request)
                .then((cachedResponse) => {
                    if (cachedResponse) {
                        // Return cached version, but also update cache in background
                        fetch(request).then((response) => {
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(request, response);
                            });
                        }).catch(() => {});
                        return cachedResponse;
                    }
                    
                    // Not in cache, fetch from network
                    return fetch(request)
                        .then((response) => {
                            const responseClone = response.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(request, responseClone);
                            });
                            return response;
                        });
                })
        );
        return;
    }

    // Default: Network first with cache fallback
    event.respondWith(
        fetch(request)
            .then((response) => {
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(request, responseClone);
                });
                return response;
            })
            .catch(() => {
                return caches.match(request);
            })
    );
});

// Handle push notifications (optional, for future use)
self.addEventListener('push', (event) => {
    if (!event.data) return;

    const data = event.data.json();
    const options = {
        body: data.body || 'Anda memiliki notifikasi baru',
        icon: '/images/pwa/icon-192x192.png',
        badge: '/images/pwa/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            url: data.url || '/dashboard'
        }
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Jari POS', options)
    );
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    const urlToOpen = event.notification.data?.url || '/dashboard';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open
                for (const client of clientList) {
                    if (client.url.includes(urlToOpen) && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Open new window if none found
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Background sync for offline transactions
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-transactions') {
        console.log('[Service Worker] Background sync: sync-transactions');
        event.waitUntil(syncOfflineTransactions());
    }
});

/**
 * Sync offline transactions to server
 */
async function syncOfflineTransactions() {
    try {
        // Open IndexedDB to get pending transactions
        const db = await openIndexedDB();
        const transactions = await getPendingTransactionsFromDB(db);
        
        if (transactions.length === 0) {
            console.log('[Service Worker] No pending transactions to sync');
            return;
        }
        
        console.log(`[Service Worker] Syncing ${transactions.length} transactions...`);
        
        // Get CSRF token from a client
        const allClients = await clients.matchAll();
        let csrfToken = '';
        
        for (const client of allClients) {
            // Try to get CSRF from client
            try {
                const response = await client.fetch('/pos');
                const html = await response.text();
                const match = html.match(/meta name="csrf-token" content="([^"]+)"/);
                if (match) {
                    csrfToken = match[1];
                    break;
                }
            } catch (e) {
                // Ignore
            }
        }
        
        // Send transactions to server
        const response = await fetch('/pos/sync/transactions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ transactions })
        });
        
        if (response.ok) {
            const result = await response.json();
            console.log('[Service Worker] Sync result:', result);
            
            // Remove synced transactions from IndexedDB
            if (result.results?.accepted) {
                for (const txn of result.results.accepted) {
                    await removeTransactionFromDB(db, txn.client_id);
                }
            }
            
            // Notify clients about sync completion
            const clients = await self.clients.matchAll();
            clients.forEach(client => {
                client.postMessage({
                    type: 'SYNC_COMPLETE',
                    data: result
                });
            });
        }
    } catch (error) {
        console.error('[Service Worker] Sync failed:', error);
        throw error; // Will trigger retry
    }
}

/**
 * Open IndexedDB
 */
function openIndexedDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('jaripos-offline', 1);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Get pending transactions from IndexedDB
 */
function getPendingTransactionsFromDB(db) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction('pendingTransactions', 'readonly');
        const store = transaction.objectStore('pendingTransactions');
        const index = store.index('synced');
        const request = index.getAll(false);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Remove transaction from IndexedDB
 */
function removeTransactionFromDB(db, clientId) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction('pendingTransactions', 'readwrite');
        const store = transaction.objectStore('pendingTransactions');
        const request = store.delete(clientId);
        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
    });
}

// Listen for messages from clients
self.addEventListener('message', (event) => {
    if (event.data?.type === 'TRIGGER_SYNC') {
        console.log('[Service Worker] Manual sync triggered');
        syncOfflineTransactions();
    }
});
