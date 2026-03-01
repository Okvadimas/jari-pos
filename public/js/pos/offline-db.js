/**
 * Jari POS - Offline Database Module
 * IndexedDB management for offline functionality
 * 
 * This file is loaded in public/js/pos/ for browser access
 */

const OfflineDB = (function() {
    const DB_NAME = 'jaripos-offline';
    const DB_VERSION = 1;

    // Store names
    const STORES = {
        PRODUCTS: 'products',
        CATEGORIES: 'categories',
        PENDING_TRANSACTIONS: 'pendingTransactions',
        SYNC_HISTORY: 'syncHistory'
    };

    let db = null;

    // ============================================
    // Database Initialization
    // ============================================

    /**
     * Initialize IndexedDB
     * @returns {Promise<IDBDatabase>}
     */
    async function initDB() {
        return new Promise((resolve, reject) => {
            if (db) {
                resolve(db);
                return;
            }

            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onerror = () => {
                console.error('[OfflineDB] Failed to open database:', request.error);
                reject(request.error);
            };

            request.onsuccess = () => {
                db = request.result;
                console.log('[OfflineDB] Database opened successfully');
                resolve(db);
            };

            request.onupgradeneeded = (event) => {
                const database = event.target.result;
                console.log('[OfflineDB] Upgrading database...');

                // Products store with index on category_id
                if (!database.objectStoreNames.contains(STORES.PRODUCTS)) {
                    const productStore = database.createObjectStore(STORES.PRODUCTS, { keyPath: 'id' });
                    productStore.createIndex('category_id', 'category_id', { unique: false });
                    productStore.createIndex('name', 'name', { unique: false });
                }

                // Categories store
                if (!database.objectStoreNames.contains(STORES.CATEGORIES)) {
                    database.createObjectStore(STORES.CATEGORIES, { keyPath: 'id' });
                }

                // Pending transactions store
                if (!database.objectStoreNames.contains(STORES.PENDING_TRANSACTIONS)) {
                    const txnStore = database.createObjectStore(STORES.PENDING_TRANSACTIONS, { 
                        keyPath: 'client_id', 
                        autoIncrement: false 
                    });
                    txnStore.createIndex('created_at', 'created_at', { unique: false });
                }

                // Sync history store
                if (!database.objectStoreNames.contains(STORES.SYNC_HISTORY)) {
                    const historyStore = database.createObjectStore(STORES.SYNC_HISTORY, { 
                        keyPath: 'id', 
                        autoIncrement: true 
                    });
                    historyStore.createIndex('synced_at', 'synced_at', { unique: false });
                }
            };
        });
    }

    // ============================================
    // Product Cache Functions
    // ============================================

    /**
     * Cache products for offline use
     * @param {Array} products - Array of product objects
     * @returns {Promise<void>}
     */
    async function cacheProducts(products) {
        // Defensive: pastikan products adalah array
        if (!Array.isArray(products)) {
            console.warn('[OfflineDB] cacheProducts received non-array:', typeof products, products);
            return;
        }

        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PRODUCTS], 'readwrite');
            const store = transaction.objectStore(STORES.PRODUCTS);
            
            products.forEach(product => {
                // Add local_stock field for offline tracking
                store.put({
                    ...product,
                    local_stock: product.stock ?? 999,
                    cached_at: new Date().toISOString()
                });
            });
            
            transaction.oncomplete = () => {
                console.log('[OfflineDB] Products cached:', products.length);
                resolve();
            };
            
            transaction.onerror = () => reject(transaction.error);
        });
    }

    /**
     * Get cached products
     * @param {string|number} categoryId - Optional category filter
     * @param {string} search - Optional search query
     * @returns {Promise<Array>}
     */
    async function getCachedProducts(categoryId, search) {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PRODUCTS], 'readonly');
            const store = transaction.objectStore(STORES.PRODUCTS);
            const request = store.getAll();
            
            request.onsuccess = () => {
                let products = request.result || [];
                
                // Filter by category if specified
                if (categoryId && categoryId !== 'all') {
                    products = products.filter(p => p.category_id == categoryId);
                }
                
                // Filter by search if specified
                if (search) {
                    const searchLower = search.toLowerCase();
                    products = products.filter(p => 
                        p.name?.toLowerCase().includes(searchLower)
                    );
                }
                
                resolve(products);
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    // ============================================
    // Category Cache Functions
    // ============================================

    /**
     * Cache categories for offline use
     * @param {Array} categories - Array of category objects
     * @returns {Promise<void>}
     */
    async function cacheCategories(categories) {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.CATEGORIES], 'readwrite');
            const store = transaction.objectStore(STORES.CATEGORIES);
            
            categories.forEach(category => {
                store.put({
                    ...category,
                    cached_at: new Date().toISOString()
                });
            });
            
            transaction.oncomplete = () => {
                console.log('[OfflineDB] Categories cached:', categories.length);
                resolve();
            };
            
            transaction.onerror = () => reject(transaction.error);
        });
    }

    /**
     * Get cached categories
     * @returns {Promise<Array>}
     */
    async function getCachedCategories() {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.CATEGORIES], 'readonly');
            const store = transaction.objectStore(STORES.CATEGORIES);
            const request = store.getAll();
            
            request.onsuccess = () => resolve(request.result || []);
            request.onerror = () => reject(request.error);
        });
    }

    // ============================================
    // Pending Transaction Functions
    // ============================================

    /**
     * Save a transaction to the pending queue
     * @param {Object} transaction - Transaction data
     * @returns {Promise<string>} - Client ID
     */
    async function savePendingTransaction(transaction) {
        if (!db) await initDB();
        
        const clientId = `offline_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        return new Promise((resolve, reject) => {
            const tx = db.transaction([STORES.PENDING_TRANSACTIONS], 'readwrite');
            const store = tx.objectStore(STORES.PENDING_TRANSACTIONS);
            
            const txnData = {
                client_id: clientId,
                ...transaction,
                created_at: new Date().toISOString(),
                status: 'pending'
            };
            
            const request = store.add(txnData);
            
            request.onsuccess = () => {
                console.log('[OfflineDB] Transaction saved:', clientId);
                resolve(clientId);
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Get all pending transactions
     * @returns {Promise<Array>}
     */
    async function getPendingTransactions() {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PENDING_TRANSACTIONS], 'readonly');
            const store = transaction.objectStore(STORES.PENDING_TRANSACTIONS);
            const request = store.getAll();
            
            request.onsuccess = () => resolve(request.result || []);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Remove a pending transaction (after successful sync)
     * @param {string} clientId - Client-generated ID
     * @returns {Promise<void>}
     */
    async function removePendingTransaction(clientId) {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PENDING_TRANSACTIONS], 'readwrite');
            const store = transaction.objectStore(STORES.PENDING_TRANSACTIONS);
            const request = store.delete(clientId);
            
            request.onsuccess = () => {
                console.log('[OfflineDB] Transaction removed:', clientId);
                resolve();
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    // ============================================
    // Local Stock Tracking
    // ============================================

    /**
     * Reduce local stock for a product
     * @param {number} productId
     * @param {number} quantity
     * @returns {Promise<void>}
     */
    async function reduceLocalStock(productId, quantity) {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PRODUCTS], 'readwrite');
            const store = transaction.objectStore(STORES.PRODUCTS);
            const request = store.get(productId);
            
            request.onsuccess = () => {
                const product = request.result;
                if (product) {
                    product.local_stock = Math.max(0, (product.local_stock || product.stock || 0) - quantity);
                    store.put(product);
                }
                resolve();
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Restore local stock for a product (when removing from cart)
     * @param {number} productId
     * @param {number} quantity
     * @returns {Promise<void>}
     */
    async function restoreLocalStock(productId, quantity) {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PRODUCTS], 'readwrite');
            const store = transaction.objectStore(STORES.PRODUCTS);
            const request = store.get(productId);
            
            request.onsuccess = () => {
                const product = request.result;
                if (product) {
                    product.local_stock = (product.local_stock || 0) + quantity;
                    store.put(product);
                }
                resolve();
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Sync stock from server (reset local stock to server values)
     * @param {Array} products - Fresh product data from server
     * @returns {Promise<void>}
     */
    async function syncStockFromServer(products) {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PRODUCTS], 'readwrite');
            const store = transaction.objectStore(STORES.PRODUCTS);
            
            products.forEach(product => {
                store.put({
                    ...product,
                    local_stock: product.stock ?? 999,
                    cached_at: new Date().toISOString()
                });
            });
            
            transaction.oncomplete = () => {
                console.log('[OfflineDB] Stock synced from server');
                resolve();
            };
            
            transaction.onerror = () => reject(transaction.error);
        });
    }

    /**
     * Get local stock for a product
     * @param {number} productId
     * @returns {Promise<number>}
     */
    async function getLocalStock(productId) {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PRODUCTS], 'readonly');
            const store = transaction.objectStore(STORES.PRODUCTS);
            const request = store.get(productId);
            
            request.onsuccess = () => {
                const product = request.result;
                resolve(product?.local_stock ?? product?.stock ?? 0);
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    // ============================================
    // Cache Cleanup Functions
    // ============================================

    /**
     * Clear product cache
     * @returns {Promise<void>}
     */
    async function clearProductCache() {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.PRODUCTS], 'readwrite');
            const store = transaction.objectStore(STORES.PRODUCTS);
            const request = store.clear();
            
            request.onsuccess = () => {
                console.log('[OfflineDB] Product cache cleared');
                resolve();
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Clear category cache
     * @returns {Promise<void>}
     */
    async function clearCategoryCache() {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.CATEGORIES], 'readwrite');
            const store = transaction.objectStore(STORES.CATEGORIES);
            const request = store.clear();
            
            request.onsuccess = () => {
                console.log('[OfflineDB] Category cache cleared');
                resolve();
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Clear sync history
     * @returns {Promise<void>}
     */
    async function clearSyncHistory() {
        if (!db) await initDB();
        
        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORES.SYNC_HISTORY], 'readwrite');
            const store = transaction.objectStore(STORES.SYNC_HISTORY);
            const request = store.clear();
            
            request.onsuccess = () => {
                console.log('[OfflineDB] Sync history cleared');
                resolve();
            };
            
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Clear all cache (except pending transactions)
     * @returns {Promise<void>}
     */
    async function clearAllCache() {
        await clearProductCache();
        await clearCategoryCache();
        await clearSyncHistory();
        console.log('[OfflineDB] All cache cleared');
    }

    // ============================================
    // Status & Info Functions
    // ============================================

    /**
     * Get offline status
     * @returns {Promise<Object>}
     */
    async function getOfflineStatus() {
        const pending = await getPendingTransactions();
        return {
            isOffline: !navigator.onLine,
            pendingCount: pending.length,
            pendingTransactions: pending
        };
    }

    /**
     * Get storage info
     * @returns {Promise<Object>}
     */
    async function getStorageInfo() {
        if (!db) await initDB();
        
        const products = await getCachedProducts();
        const categories = await getCachedCategories();
        const pending = await getPendingTransactions();
        
        return {
            products: products.length,
            categories: categories.length,
            pending: pending.length
        };
    }

    // ============================================
    // Public API
    // ============================================
    return {
        initDB,
        cacheProducts,
        getCachedProducts,
        cacheCategories,
        getCachedCategories,
        savePendingTransaction,
        getPendingTransactions,
        removePendingTransaction,
        reduceLocalStock,
        restoreLocalStock,
        syncStockFromServer,
        getLocalStock,
        clearProductCache,
        clearCategoryCache,
        clearSyncHistory,
        clearAllCache,
        getOfflineStatus,
        getStorageInfo
    };
})();

// Make globally available
window.OfflineDB = OfflineDB;
