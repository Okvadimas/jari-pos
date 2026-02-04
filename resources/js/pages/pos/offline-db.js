/**
 * Jari POS - Offline Database Module
 * IndexedDB management for offline functionality
 */

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
export async function initDB() {
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

            // Pending transactions store with auto-increment client_id
            if (!database.objectStoreNames.contains(STORES.PENDING_TRANSACTIONS)) {
                const txnStore = database.createObjectStore(STORES.PENDING_TRANSACTIONS, { 
                    keyPath: 'client_id',
                    autoIncrement: true 
                });
                txnStore.createIndex('created_at', 'created_at', { unique: false });
                txnStore.createIndex('synced', 'synced', { unique: false });
            }

            // Sync history store
            if (!database.objectStoreNames.contains(STORES.SYNC_HISTORY)) {
                const historyStore = database.createObjectStore(STORES.SYNC_HISTORY, { 
                    keyPath: 'id',
                    autoIncrement: true 
                });
                historyStore.createIndex('timestamp', 'timestamp', { unique: false });
            }

            console.log('[OfflineDB] Database upgrade complete');
        };
    });
}

/**
 * Get database instance
 * @returns {Promise<IDBDatabase>}
 */
async function getDB() {
    if (!db) {
        await initDB();
    }
    return db;
}

// ============================================
// Product Cache Functions
// ============================================

/**
 * Cache products from server
 * @param {Array} products - Array of product objects
 */
export async function cacheProducts(products) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PRODUCTS, 'readwrite');
    const store = transaction.objectStore(STORES.PRODUCTS);

    // Clear existing products first
    store.clear();

    // Add all products with local_stock field
    products.forEach(product => {
        const productWithStock = {
            ...product,
            local_stock: product.stock ?? 999, // Default high stock if not provided
            cached_at: new Date().toISOString()
        };
        store.add(productWithStock);
    });

    return new Promise((resolve, reject) => {
        transaction.oncomplete = () => {
            console.log(`[OfflineDB] Cached ${products.length} products`);
            resolve();
        };
        transaction.onerror = () => reject(transaction.error);
    });
}

/**
 * Get cached products
 * @param {string|null} categoryId - Optional category filter
 * @param {string|null} search - Optional search term
 * @returns {Promise<Array>}
 */
export async function getCachedProducts(categoryId = null, search = null) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PRODUCTS, 'readonly');
    const store = transaction.objectStore(STORES.PRODUCTS);

    return new Promise((resolve, reject) => {
        let request;
        
        if (categoryId && categoryId !== 'all') {
            const index = store.index('category_id');
            request = index.getAll(parseInt(categoryId));
        } else {
            request = store.getAll();
        }

        request.onsuccess = () => {
            let products = request.result;

            // Apply search filter if provided
            if (search) {
                const searchLower = search.toLowerCase();
                products = products.filter(p => 
                    p.name?.toLowerCase().includes(searchLower) ||
                    p.description?.toLowerCase().includes(searchLower)
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
 * Cache categories from server
 * @param {Array} categories 
 */
export async function cacheCategories(categories) {
    const database = await getDB();
    const transaction = database.transaction(STORES.CATEGORIES, 'readwrite');
    const store = transaction.objectStore(STORES.CATEGORIES);

    store.clear();
    categories.forEach(category => store.add(category));

    return new Promise((resolve, reject) => {
        transaction.oncomplete = () => {
            console.log(`[OfflineDB] Cached ${categories.length} categories`);
            resolve();
        };
        transaction.onerror = () => reject(transaction.error);
    });
}

/**
 * Get cached categories
 * @returns {Promise<Array>}
 */
export async function getCachedCategories() {
    const database = await getDB();
    const transaction = database.transaction(STORES.CATEGORIES, 'readonly');
    const store = transaction.objectStore(STORES.CATEGORIES);

    return new Promise((resolve, reject) => {
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

// ============================================
// Pending Transaction Functions
// ============================================

/**
 * Save a pending transaction (offline order)
 * @param {Object} transaction - Transaction data
 * @returns {Promise<number>} - Client ID of saved transaction
 */
export async function savePendingTransaction(transactionData) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PENDING_TRANSACTIONS, 'readwrite');
    const store = transaction.objectStore(STORES.PENDING_TRANSACTIONS);

    const pendingTxn = {
        ...transactionData,
        created_at: new Date().toISOString(),
        synced: false
    };

    return new Promise((resolve, reject) => {
        const request = store.add(pendingTxn);
        request.onsuccess = () => {
            console.log('[OfflineDB] Saved pending transaction:', request.result);
            resolve(request.result);
        };
        request.onerror = () => reject(request.error);
    });
}

/**
 * Get all pending (unsynced) transactions
 * @returns {Promise<Array>}
 */
export async function getPendingTransactions() {
    const database = await getDB();
    const transaction = database.transaction(STORES.PENDING_TRANSACTIONS, 'readonly');
    const store = transaction.objectStore(STORES.PENDING_TRANSACTIONS);
    const index = store.index('synced');

    return new Promise((resolve, reject) => {
        const request = index.getAll(false);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Remove a pending transaction after successful sync
 * @param {number} clientId 
 */
export async function removePendingTransaction(clientId) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PENDING_TRANSACTIONS, 'readwrite');
    const store = transaction.objectStore(STORES.PENDING_TRANSACTIONS);

    return new Promise((resolve, reject) => {
        const request = store.delete(clientId);
        request.onsuccess = () => {
            console.log('[OfflineDB] Removed pending transaction:', clientId);
            resolve();
        };
        request.onerror = () => reject(request.error);
    });
}

/**
 * Mark transaction as synced (alternative to delete)
 * @param {number} clientId 
 * @param {number} serverId - Server-assigned order ID
 */
export async function markTransactionSynced(clientId, serverId) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PENDING_TRANSACTIONS, 'readwrite');
    const store = transaction.objectStore(STORES.PENDING_TRANSACTIONS);

    return new Promise((resolve, reject) => {
        const getRequest = store.get(clientId);
        getRequest.onsuccess = () => {
            const txn = getRequest.result;
            if (txn) {
                txn.synced = true;
                txn.server_id = serverId;
                txn.synced_at = new Date().toISOString();
                const putRequest = store.put(txn);
                putRequest.onsuccess = () => resolve();
                putRequest.onerror = () => reject(putRequest.error);
            } else {
                resolve();
            }
        };
        getRequest.onerror = () => reject(getRequest.error);
    });
}

// ============================================
// Local Stock Tracking Functions
// ============================================

/**
 * Get local stock for a product
 * @param {number} productId 
 * @returns {Promise<number>}
 */
export async function getLocalStock(productId) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PRODUCTS, 'readonly');
    const store = transaction.objectStore(STORES.PRODUCTS);

    return new Promise((resolve, reject) => {
        const request = store.get(productId);
        request.onsuccess = () => {
            const product = request.result;
            resolve(product?.local_stock ?? 0);
        };
        request.onerror = () => reject(request.error);
    });
}

/**
 * Reduce local stock when adding to cart
 * @param {number} productId 
 * @param {number} qty 
 */
export async function reduceLocalStock(productId, qty) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PRODUCTS, 'readwrite');
    const store = transaction.objectStore(STORES.PRODUCTS);

    return new Promise((resolve, reject) => {
        const getRequest = store.get(productId);
        getRequest.onsuccess = () => {
            const product = getRequest.result;
            if (product) {
                product.local_stock = Math.max(0, (product.local_stock ?? 0) - qty);
                const putRequest = store.put(product);
                putRequest.onsuccess = () => resolve(product.local_stock);
                putRequest.onerror = () => reject(putRequest.error);
            } else {
                resolve(0);
            }
        };
        getRequest.onerror = () => reject(getRequest.error);
    });
}

/**
 * Restore local stock when removing from cart
 * @param {number} productId 
 * @param {number} qty 
 */
export async function restoreLocalStock(productId, qty) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PRODUCTS, 'readwrite');
    const store = transaction.objectStore(STORES.PRODUCTS);

    return new Promise((resolve, reject) => {
        const getRequest = store.get(productId);
        getRequest.onsuccess = () => {
            const product = getRequest.result;
            if (product) {
                product.local_stock = (product.local_stock ?? 0) + qty;
                const putRequest = store.put(product);
                putRequest.onsuccess = () => resolve(product.local_stock);
                putRequest.onerror = () => reject(putRequest.error);
            } else {
                resolve(0);
            }
        };
        getRequest.onerror = () => reject(getRequest.error);
    });
}

/**
 * Sync stock from server (refresh local stock with server data)
 * @param {Array} products 
 */
export async function syncStockFromServer(products) {
    const database = await getDB();
    const transaction = database.transaction(STORES.PRODUCTS, 'readwrite');
    const store = transaction.objectStore(STORES.PRODUCTS);

    return new Promise((resolve, reject) => {
        let updated = 0;
        products.forEach(product => {
            const getRequest = store.get(product.id);
            getRequest.onsuccess = () => {
                const existing = getRequest.result;
                if (existing) {
                    existing.local_stock = product.stock ?? existing.local_stock;
                    existing.stock = product.stock;
                    existing.cached_at = new Date().toISOString();
                    store.put(existing);
                    updated++;
                }
            };
        });
        
        transaction.oncomplete = () => {
            console.log(`[OfflineDB] Synced stock for ${updated} products`);
            resolve(updated);
        };
        transaction.onerror = () => reject(transaction.error);
    });
}

/**
 * Get products with zero local stock
 * @returns {Promise<Array>}
 */
export async function getOutOfStockProducts() {
    const products = await getCachedProducts();
    return products.filter(p => (p.local_stock ?? 0) <= 0);
}

// ============================================
// Sync History Functions
// ============================================

/**
 * Add sync history record
 * @param {Object} syncResult 
 */
export async function addSyncHistory(syncResult) {
    const database = await getDB();
    const transaction = database.transaction(STORES.SYNC_HISTORY, 'readwrite');
    const store = transaction.objectStore(STORES.SYNC_HISTORY);

    const record = {
        ...syncResult,
        timestamp: new Date().toISOString()
    };

    return new Promise((resolve, reject) => {
        const request = store.add(record);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Get sync history
 * @param {number} limit 
 * @returns {Promise<Array>}
 */
export async function getSyncHistory(limit = 50) {
    const database = await getDB();
    const transaction = database.transaction(STORES.SYNC_HISTORY, 'readonly');
    const store = transaction.objectStore(STORES.SYNC_HISTORY);

    return new Promise((resolve, reject) => {
        const request = store.getAll();
        request.onsuccess = () => {
            // Sort by timestamp descending and limit
            const history = request.result
                .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
                .slice(0, limit);
            resolve(history);
        };
        request.onerror = () => reject(request.error);
    });
}

// ============================================
// Manual Cleanup Functions
// ============================================

/**
 * Clear product cache
 */
export async function clearProductCache() {
    const database = await getDB();
    const transaction = database.transaction(STORES.PRODUCTS, 'readwrite');
    const store = transaction.objectStore(STORES.PRODUCTS);

    return new Promise((resolve, reject) => {
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
 */
export async function clearCategoryCache() {
    const database = await getDB();
    const transaction = database.transaction(STORES.CATEGORIES, 'readwrite');
    const store = transaction.objectStore(STORES.CATEGORIES);

    return new Promise((resolve, reject) => {
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
 */
export async function clearSyncHistory() {
    const database = await getDB();
    const transaction = database.transaction(STORES.SYNC_HISTORY, 'readwrite');
    const store = transaction.objectStore(STORES.SYNC_HISTORY);

    return new Promise((resolve, reject) => {
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
 */
export async function clearAllCache() {
    await clearProductCache();
    await clearCategoryCache();
    await clearSyncHistory();
    console.log('[OfflineDB] All cache cleared (pending transactions preserved)');
}

// ============================================
// Status & Info Functions
// ============================================

/**
 * Get offline status information
 * @returns {Promise<Object>}
 */
export async function getOfflineStatus() {
    const pending = await getPendingTransactions();
    const products = await getCachedProducts();
    const categories = await getCachedCategories();

    return {
        hasPendingTransactions: pending.length > 0,
        pendingCount: pending.length,
        cachedProducts: products.length,
        cachedCategories: categories.length
    };
}

/**
 * Get storage info
 * @returns {Promise<Object>}
 */
export async function getStorageInfo() {
    const products = await getCachedProducts();
    const categories = await getCachedCategories();
    const pending = await getPendingTransactions();
    const history = await getSyncHistory();

    return {
        products: products.length,
        categories: categories.length,
        pending: pending.length,
        syncHistory: history.length
    };
}

// Export as default object for window attachment
const OfflineDB = {
    initDB,
    // Products
    cacheProducts,
    getCachedProducts,
    // Categories
    cacheCategories,
    getCachedCategories,
    // Pending Transactions
    savePendingTransaction,
    getPendingTransactions,
    removePendingTransaction,
    markTransactionSynced,
    // Local Stock
    getLocalStock,
    reduceLocalStock,
    restoreLocalStock,
    syncStockFromServer,
    getOutOfStockProducts,
    // Sync History
    addSyncHistory,
    getSyncHistory,
    // Cleanup
    clearProductCache,
    clearCategoryCache,
    clearSyncHistory,
    clearAllCache,
    // Status
    getOfflineStatus,
    getStorageInfo
};

// Attach to window for global access
window.OfflineDB = OfflineDB;

export default OfflineDB;
