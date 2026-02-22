/**
 * Thermal Printer Module via Web Bluetooth
 * Compatible with: VSC MP-58C and other ESC/POS Bluetooth printers
 * 
 * Uses Web Bluetooth API to connect and send ESC/POS commands
 */

const ThermalPrinter = (function () {
    // ============================================
    // State
    // ============================================
    let device = null;
    let server = null;
    let characteristic = null;
    let isConnectedState = false;

    // Bluetooth Service/Characteristic UUIDs for Serial Port Profile
    // Common UUIDs for BLE thermal printers
    const PRINTER_SERVICE_UUID = '000018f0-0000-1000-8000-00805f9b34fb';
    const PRINTER_CHARACTERISTIC_UUID = '00002af1-0000-1000-8000-00805f9b34fb';

    // Known service UUIDs for various thermal printers (RPP02N, VSC MP-58C, etc.)
    const FALLBACK_SERVICES = [
        '000018f0-0000-1000-8000-00805f9b34fb',  // Common ESC/POS
        '0000ff00-0000-1000-8000-00805f9b34fb',  // Generic printer
        '49535343-fe7d-4ae5-8fa9-9fafd205e455',  // ISSC / Microchip 
        'e7810a71-73ae-499d-8c15-faa9aef0c3f2',  // Nordic UART
        '0000ffe0-0000-1000-8000-00805f9b34fb',  // HM-10/CC2541 (RPP02N)
        '0000fee7-0000-1000-8000-00805f9b34fb',  // Tencent BLE
        '0000fff0-0000-1000-8000-00805f9b34fb',  // Generic Chinese BLE
        '00001101-0000-1000-8000-00805f9b34fb',  // SPP (classic Bluetooth)
        '0000ae00-0000-1000-8000-00805f9b34fb',  // Some RPP02N units
        '0000ae30-0000-1000-8000-00805f9b34fb',  // Rongta RPP series
    ];

    const FALLBACK_CHARACTERISTICS = [
        '00002af1-0000-1000-8000-00805f9b34fb',
        '0000ff02-0000-1000-8000-00805f9b34fb',
        '49535343-8841-43f4-a8d4-ecbe34729bb3',
        'bef8d6c9-9c21-4c9e-b632-bd58c1009f9f',
        '0000ffe1-0000-1000-8000-00805f9b34fb',  // HM-10/CC2541
        '0000fff1-0000-1000-8000-00805f9b34fb',  // Generic Chinese BLE
        '0000fff2-0000-1000-8000-00805f9b34fb',  // Write characteristic
        '0000ae01-0000-1000-8000-00805f9b34fb',  // Rongta RPP
        '0000ae31-0000-1000-8000-00805f9b34fb',  // Rongta RPP write
    ];

    // ESC/POS Command Constants
    const ESC = 0x1B;
    const GS = 0x1D;
    const LF = 0x0A;

    const COMMANDS = {
        INIT: [ESC, 0x40],                          // Initialize printer
        ALIGN_LEFT: [ESC, 0x61, 0x00],              // Left align
        ALIGN_CENTER: [ESC, 0x61, 0x01],            // Center align
        ALIGN_RIGHT: [ESC, 0x61, 0x02],             // Right align
        BOLD_ON: [ESC, 0x45, 0x01],                 // Bold on
        BOLD_OFF: [ESC, 0x45, 0x00],                // Bold off
        FONT_NORMAL: [GS, 0x21, 0x00],              // Normal size
        FONT_DOUBLE_HEIGHT: [GS, 0x21, 0x01],       // Double height
        FONT_DOUBLE_WIDTH: [GS, 0x21, 0x10],        // Double width
        FONT_DOUBLE: [GS, 0x21, 0x11],              // Double width + height
        UNDERLINE_ON: [ESC, 0x2D, 0x01],            // Underline on
        UNDERLINE_OFF: [ESC, 0x2D, 0x00],           // Underline off
        CUT_PAPER: [GS, 0x56, 0x00],                // Full cut
        CUT_PAPER_PARTIAL: [GS, 0x56, 0x01],        // Partial cut
        FEED_LINE: [LF],                             // Line feed
        FEED_LINES: (n) => [ESC, 0x64, n],          // Feed n lines
    };

    // Max characters per line for 58mm printer
    const LINE_WIDTH = 32;

    // ============================================
    // LocalStorage Keys
    // ============================================
    const STORAGE_KEY = 'pos-thermal-printer';

    function getSavedPrinter() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            return saved ? JSON.parse(saved) : null;
        } catch {
            return null;
        }
    }

    function savePrinterInfo(deviceName, deviceId) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
            name: deviceName,
            id: deviceId,
            connectedAt: new Date().toISOString()
        }));
    }

    function clearSavedPrinter() {
        localStorage.removeItem(STORAGE_KEY);
    }

    // ============================================
    // Connection Functions
    // ============================================

    /**
     * Connect to a Bluetooth thermal printer
     * If a printer was previously connected, try to reconnect
     */
    async function connectPrinter() {
        if (!navigator.bluetooth) {
            throw new Error('Web Bluetooth API tidak didukung di browser ini. Gunakan Chrome/Edge.');
        }

        try {
            // Request Bluetooth device - accept all devices so any thermal printer can be found
            device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: FALLBACK_SERVICES
            });

            if (!device) {
                throw new Error('Tidak ada perangkat yang dipilih');
            }

            // Listen for disconnection
            device.addEventListener('gattserverdisconnected', onDisconnected);

            // Connect to GATT server
            await connectToDevice(device);

            // Save printer info for auto-reconnect
            savePrinterInfo(device.name || 'Unknown Printer', device.id);

            updateUI();
            return {
                success: true,
                name: device.name || 'Unknown Printer'
            };

        } catch (error) {
            console.error('[ThermalPrinter] Connection error:', error);
            isConnectedState = false;
            updateUI();

            if (error.name === 'NotFoundError') {
                throw new Error('Tidak ada printer yang dipilih');
            }
            throw error;
        }
    }

    /**
     * Connect to a specific Bluetooth device and find the writable characteristic
     * Uses dynamic service discovery as a primary approach
     */
    async function connectToDevice(btDevice) {
        console.log('[ThermalPrinter] Connecting to:', btDevice.name);

        server = await btDevice.gatt.connect();
        console.log('[ThermalPrinter] GATT connected, discovering services...');

        // Strategy 1: Try known service UUIDs first (faster)
        for (const svcUuid of FALLBACK_SERVICES) {
            try {
                const service = await server.getPrimaryService(svcUuid);
                const chars = await service.getCharacteristics();
                console.log(`[ThermalPrinter] Service ${svcUuid} found, ${chars.length} characteristics`);

                for (const char of chars) {
                    if (char.properties.write || char.properties.writeWithoutResponse) {
                        characteristic = char;
                        isConnectedState = true;
                        console.log('[ThermalPrinter] Connected! Service:', svcUuid, 'Char:', char.uuid);
                        console.log('[ThermalPrinter] Write:', char.properties.write, 'WriteNoResp:', char.properties.writeWithoutResponse);
                        return;
                    }
                }
            } catch (e) {
                // Service not found on this device, try next
                continue;
            }
        }

        // Strategy 2: Dynamic discovery - get ALL services
        console.log('[ThermalPrinter] Known UUIDs failed, trying dynamic discovery...');
        try {
            const services = await server.getPrimaryServices();
            console.log(`[ThermalPrinter] Found ${services.length} services:`, services.map(s => s.uuid));

            for (const service of services) {
                try {
                    const chars = await service.getCharacteristics();
                    for (const char of chars) {
                        console.log(`[ThermalPrinter] Service: ${service.uuid}, Char: ${char.uuid}, Props:`, {
                            write: char.properties.write,
                            writeWithoutResponse: char.properties.writeWithoutResponse,
                            read: char.properties.read,
                            notify: char.properties.notify
                        });

                        if (char.properties.write || char.properties.writeWithoutResponse) {
                            characteristic = char;
                            isConnectedState = true;
                            console.log('[ThermalPrinter] Connected via discovery! Service:', service.uuid, 'Char:', char.uuid);
                            return;
                        }
                    }
                } catch (charErr) {
                    console.warn('[ThermalPrinter] Error reading chars from service:', service.uuid, charErr.message);
                }
            }
        } catch (discoverErr) {
            console.error('[ThermalPrinter] Dynamic discovery failed:', discoverErr.message);
        }

        throw new Error('Tidak dapat menemukan service printer yang kompatibel. Pastikan printer mendukung Bluetooth Low Energy (BLE).');
    }

    /**
     * Try to auto-reconnect to a previously paired printer
     */
    async function autoReconnect() {
        const saved = getSavedPrinter();
        if (!saved) return false;

        if (!navigator.bluetooth || !navigator.bluetooth.getDevices) {
            console.log('[ThermalPrinter] Auto-reconnect not supported');
            return false;
        }

        try {
            const devices = await navigator.bluetooth.getDevices();
            const previousDevice = devices.find(d => d.id === saved.id || d.name === saved.name);

            if (previousDevice) {
                device = previousDevice;
                device.addEventListener('gattserverdisconnected', onDisconnected);

                // Watch for advertisement to auto-reconnect
                if (previousDevice.watchAdvertisements) {
                    const abortController = new AbortController();

                    previousDevice.addEventListener('advertisementreceived', async (event) => {
                        abortController.abort();
                        try {
                            await connectToDevice(previousDevice);
                            updateUI();
                            console.log('[ThermalPrinter] Auto-reconnected to:', previousDevice.name);
                        } catch (e) {
                            console.error('[ThermalPrinter] Auto-reconnect failed:', e);
                        }
                    }, { once: true });

                    await previousDevice.watchAdvertisements({ signal: abortController.signal });

                    // Timeout after 5 seconds
                    setTimeout(() => {
                        abortController.abort();
                    }, 5000);
                }

                return true;
            }
        } catch (e) {
            console.error('[ThermalPrinter] Auto-reconnect error:', e);
        }

        return false;
    }

    /**
     * Disconnect from the printer
     */
    function disconnectPrinter() {
        if (device && device.gatt.connected) {
            device.gatt.disconnect();
        }
        isConnectedState = false;
        characteristic = null;
        server = null;
        updateUI();
    }

    /**
     * Handle unexpected disconnection
     */
    function onDisconnected(event) {
        console.log('[ThermalPrinter] Disconnected:', event.target.name);
        isConnectedState = false;
        characteristic = null;
        server = null;
        updateUI();

        // Try to reconnect silently
        setTimeout(async () => {
            if (device && !isConnectedState) {
                try {
                    await connectToDevice(device);
                    updateUI();
                    console.log('[ThermalPrinter] Reconnected successfully');
                } catch (e) {
                    console.log('[ThermalPrinter] Reconnect failed');
                }
            }
        }, 3000);
    }

    /**
     * Check if printer is connected
     */
    function isConnected() {
        return isConnectedState && characteristic !== null;
    }

    /**
     * Get saved printer info
     */
    function getPrinterInfo() {
        if (isConnectedState && device) {
            return {
                name: device.name || 'Unknown Printer',
                connected: true
            };
        }
        const saved = getSavedPrinter();
        if (saved) {
            return {
                name: saved.name,
                connected: false,
                lastConnected: saved.connectedAt
            };
        }
        return null;
    }

    // ============================================
    // Data Sending Functions
    // ============================================

    /**
     * Send raw bytes to the printer in chunks
     * BLE has a max packet size (usually 20 bytes, some support up to 512)
     */
    async function sendData(data) {
        if (!isConnected()) {
            throw new Error('Printer tidak terhubung');
        }

        const CHUNK_SIZE = 100; // Safe chunk size for BLE
        const buffer = new Uint8Array(data);

        for (let i = 0; i < buffer.length; i += CHUNK_SIZE) {
            const chunk = buffer.slice(i, i + CHUNK_SIZE);
            try {
                if (characteristic.properties.writeWithoutResponse) {
                    await characteristic.writeValueWithoutResponse(chunk);
                } else {
                    await characteristic.writeValue(chunk);
                }
            } catch (e) {
                console.error('[ThermalPrinter] Write error at chunk', i, e);
                throw e;
            }
            // Small delay between chunks
            await new Promise(resolve => setTimeout(resolve, 50));
        }
    }

    // ============================================
    // Text Formatting Helpers
    // ============================================

    function textToBytes(text) {
        const encoder = new TextEncoder();
        return Array.from(encoder.encode(text));
    }

    function padRight(text, width) {
        if (text.length >= width) return text.substring(0, width);
        return text + ' '.repeat(width - text.length);
    }

    function padLeft(text, width) {
        if (text.length >= width) return text.substring(0, width);
        return ' '.repeat(width - text.length) + text;
    }

    function centerText(text, width) {
        if (text.length >= width) return text.substring(0, width);
        const pad = Math.floor((width - text.length) / 2);
        return ' '.repeat(pad) + text;
    }

    function formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function dashedLine(width) {
        return '-'.repeat(width);
    }

    function doubleLine(width) {
        return '='.repeat(width);
    }

    /**
     * Format a two-column line: left-aligned label, right-aligned value
     */
    function twoColumns(left, right, width) {
        const maxLeft = width - right.length - 1;
        const truncLeft = left.length > maxLeft ? left.substring(0, maxLeft) : left;
        const spaces = width - truncLeft.length - right.length;
        return truncLeft + ' '.repeat(Math.max(spaces, 1)) + right;
    }

    /**
     * Format a three-column item line
     */
    function threeColumns(name, qty, price, width) {
        const qtyStr = qty.toString();
        const priceStr = price;
        const nameWidth = width - qtyStr.length - priceStr.length - 2;
        const truncName = name.length > nameWidth ? name.substring(0, nameWidth) : name;
        const spaces1 = nameWidth - truncName.length + 1;
        const spaces2 = 1;
        return truncName + ' '.repeat(Math.max(spaces1, 1)) + qtyStr + ' '.repeat(spaces2) + priceStr;
    }

    // ============================================
    // Receipt Building
    // ============================================

    /**
     * Build ESC/POS commands for a receipt
     * @param {Object} orderData - Order data from server
     *   orderData.order - { id, invoice_number, company_name, company_address, order_date, created_at, created_by_name, total_amount, discount_amount, final_amount, payment_method_name, customer_name }
     *   orderData.details - [{ product_name, variant_name, quantity, sell_price, subtotal }]
     */
    function buildReceiptCommands(orderData) {
        const order = orderData.order;
        const details = orderData.details;
        let cmds = [];

        // Initialize
        cmds.push(...COMMANDS.INIT);

        // -- Header: Company Name --
        cmds.push(...COMMANDS.ALIGN_CENTER);
        cmds.push(...COMMANDS.BOLD_ON);
        cmds.push(...COMMANDS.FONT_DOUBLE_HEIGHT);
        cmds.push(...textToBytes(order.company_name || 'JariPOS'));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...COMMANDS.FONT_NORMAL);
        cmds.push(...COMMANDS.BOLD_OFF);

        // Address
        if (order.company_address) {
            cmds.push(...textToBytes(order.company_address));
            cmds.push(...COMMANDS.FEED_LINE);
        }
        cmds.push(...COMMANDS.FEED_LINE);

        // -- Order Info --
        cmds.push(...COMMANDS.ALIGN_LEFT);
        cmds.push(...textToBytes(dashedLine(LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        // Receipt No
        cmds.push(...textToBytes(twoColumns('No:', '#' + (order.invoice_number || String(order.id).padStart(5, '0')), LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        // Date
        const orderDate = order.order_date ? new Date(order.order_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
        cmds.push(...textToBytes(twoColumns('Tanggal:', orderDate, LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        // Time  
        const orderTime = order.created_at ? new Date(order.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
        cmds.push(...textToBytes(twoColumns('Waktu:', orderTime, LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        // Cashier
        cmds.push(...textToBytes(twoColumns('Kasir:', order.created_by_name || '-', LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        // Customer
        if (order.customer_name) {
            cmds.push(...textToBytes(twoColumns('Pelanggan:', order.customer_name, LINE_WIDTH)));
            cmds.push(...COMMANDS.FEED_LINE);
        }

        cmds.push(...textToBytes(dashedLine(LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        // -- Items --
        details.forEach(item => {
            const name = item.product_name + (item.variant_name ? ' (' + item.variant_name + ')' : '');
            const price = new Intl.NumberFormat('id-ID').format(item.sell_price);
            const subtotal = new Intl.NumberFormat('id-ID').format(item.subtotal);

            // Item name (full width, may wrap)
            if (name.length > LINE_WIDTH) {
                cmds.push(...textToBytes(name.substring(0, LINE_WIDTH)));
                cmds.push(...COMMANDS.FEED_LINE);
                if (name.length > LINE_WIDTH) {
                    cmds.push(...textToBytes(name.substring(LINE_WIDTH)));
                    cmds.push(...COMMANDS.FEED_LINE);
                }
            } else {
                cmds.push(...textToBytes(name));
                cmds.push(...COMMANDS.FEED_LINE);
            }

            // Qty x Price = Subtotal
            const detail = `  ${item.quantity} x ${price}`;
            cmds.push(...textToBytes(twoColumns(detail, subtotal, LINE_WIDTH)));
            cmds.push(...COMMANDS.FEED_LINE);
        });

        cmds.push(...textToBytes(dashedLine(LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        // -- Totals --
        cmds.push(...textToBytes(twoColumns('Subtotal', formatCurrency(order.total_amount), LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        if (order.discount_amount && order.discount_amount > 0) {
            const discountLabel = 'Diskon' + (order.promo_name ? ' (' + order.promo_name + ')' : '');
            cmds.push(...textToBytes(twoColumns(discountLabel, '-' + formatCurrency(order.discount_amount), LINE_WIDTH)));
            cmds.push(...COMMANDS.FEED_LINE);
        }

        cmds.push(...textToBytes(doubleLine(LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);

        // Final Amount (bold, larger)
        cmds.push(...COMMANDS.BOLD_ON);
        cmds.push(...COMMANDS.FONT_DOUBLE_HEIGHT);
        cmds.push(...textToBytes(twoColumns('TOTAL', formatCurrency(order.final_amount), LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...COMMANDS.FONT_NORMAL);
        cmds.push(...COMMANDS.BOLD_OFF);

        // Payment method
        if (order.payment_method_name) {
            cmds.push(...textToBytes(twoColumns('Pembayaran:', order.payment_method_name, LINE_WIDTH)));
            cmds.push(...COMMANDS.FEED_LINE);
        }

        cmds.push(...COMMANDS.FEED_LINE);

        // -- Footer --
        cmds.push(...COMMANDS.ALIGN_CENTER);
        cmds.push(...textToBytes(dashedLine(LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...textToBytes('Terima Kasih!'));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...textToBytes('Selamat Datang Kembali'));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...COMMANDS.FEED_LINE);

        // JariPOS branding
        cmds.push(...textToBytes('Powered by JariPOS'));
        cmds.push(...COMMANDS.FEED_LINE);

        // Feed and cut
        cmds.push(...COMMANDS.FEED_LINES(4));
        cmds.push(...COMMANDS.CUT_PAPER_PARTIAL);

        return cmds;
    }

    // ============================================
    // Print Functions
    // ============================================

    /**
     * Print a receipt from order data
     * @param {Object} orderData - { order: {...}, details: [...] }
     */
    async function printReceipt(orderData) {
        if (!isConnected()) {
            throw new Error('Printer tidak terhubung. Silakan hubungkan printer terlebih dahulu.');
        }

        const commands = buildReceiptCommands(orderData);
        await sendData(commands);
    }

    /**
     * Print a test page
     */
    async function printTestPage() {
        if (!isConnected()) {
            throw new Error('Printer tidak terhubung');
        }

        let cmds = [];
        cmds.push(...COMMANDS.INIT);
        cmds.push(...COMMANDS.ALIGN_CENTER);
        cmds.push(...COMMANDS.BOLD_ON);
        cmds.push(...COMMANDS.FONT_DOUBLE);
        cmds.push(...textToBytes('TEST PRINT'));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...COMMANDS.FONT_NORMAL);
        cmds.push(...COMMANDS.BOLD_OFF);
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...textToBytes('JariPOS Thermal Printer'));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...textToBytes('Status: OK'));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...textToBytes(dashedLine(LINE_WIDTH)));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...textToBytes(new Date().toLocaleString('id-ID')));
        cmds.push(...COMMANDS.FEED_LINE);
        cmds.push(...COMMANDS.FEED_LINES(3));
        cmds.push(...COMMANDS.CUT_PAPER_PARTIAL);

        await sendData(cmds);
    }

    // ============================================
    // UI Update
    // ============================================

    function updateUI() {
        const statusEl = document.getElementById('printerStatusIndicator');
        const statusTextEl = document.getElementById('printerStatusText');
        const navBtnEl = document.getElementById('printerNavBtn');
        const connectBtn = document.getElementById('btnConnectPrinter');
        const disconnectBtn = document.getElementById('btnDisconnectPrinter');
        const printerNameEl = document.getElementById('connectedPrinterName');
        const printReceiptBtns = document.querySelectorAll('.btn-print-receipt');

        if (isConnectedState) {
            const name = device ? device.name : 'Printer';
            if (statusEl) statusEl.classList.add('connected');
            if (statusTextEl) statusTextEl.textContent = `Terhubung: ${name}`;
            if (navBtnEl) navBtnEl.classList.add('printer-connected');
            if (navBtnEl) navBtnEl.title = `Printer: ${name} (Terhubung)`;
            if (connectBtn) connectBtn.style.display = 'none';
            if (disconnectBtn) disconnectBtn.style.display = '';
            if (printerNameEl) printerNameEl.textContent = name;

            // Enable print receipt buttons
            printReceiptBtns.forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('disabled');
            });
        } else {
            if (statusEl) statusEl.classList.remove('connected');
            if (statusTextEl) statusTextEl.textContent = 'Tidak terhubung';
            if (navBtnEl) navBtnEl.classList.remove('printer-connected');
            if (navBtnEl) navBtnEl.title = 'Printer: Tidak Terhubung';
            if (connectBtn) connectBtn.style.display = '';
            if (disconnectBtn) disconnectBtn.style.display = 'none';
            if (printerNameEl) printerNameEl.textContent = '-';

            // Disable print receipt buttons (but don't hide — user can still see them)
            printReceiptBtns.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('disabled');
            });
        }
    }

    /**
     * Forget saved printer (for switching devices)
     */
    function forgetPrinter() {
        disconnectPrinter();
        clearSavedPrinter();
        updateUI();
    }

    // ============================================
    // Public API
    // ============================================
    return {
        connectPrinter,
        disconnectPrinter,
        forgetPrinter,
        autoReconnect,
        isConnected,
        getPrinterInfo,
        printReceipt,
        printTestPage,
        updateUI,
        getSavedPrinter
    };

})();

// Export globally
window.ThermalPrinter = ThermalPrinter;
