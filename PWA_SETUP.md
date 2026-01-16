# PWA (Progressive Web App) Setup

Dokumentasi ini menjelaskan setup PWA untuk aplikasi Jari POS.

## Apa itu PWA?

Progressive Web App (PWA) memungkinkan website dapat di-install di smartphone seperti aplikasi native. Fitur-fiturnya termasuk:

- **Installable** - Dapat di-install di home screen smartphone
- **Offline Support** - Aplikasi tetap dapat diakses saat offline
- **Fast Loading** - Caching untuk loading yang lebih cepat
- **Push Notifications** - Dukungan notifikasi (opsional)

## File PWA

### 1. manifest.json (`public/manifest.json`)
Berisi metadata aplikasi seperti nama, icon, theme color, dll.

### 2. Service Worker (`public/sw.js`)
Menangani caching dan offline functionality dengan strategi:
- **Network First** untuk halaman HTML
- **Cache First** untuk asset statis (CSS, JS, images)

### 3. Offline Page (`resources/views/offline.blade.php`)
Halaman yang ditampilkan saat user offline dan halaman yang diminta tidak ada di cache.

### 4. PWA Icons (`public/images/pwa/`)
Icon dalam berbagai ukuran:
- icon-72x72.png
- icon-96x96.png
- icon-128x128.png
- icon-144x144.png
- icon-152x152.png
- icon-192x192.png
- icon-384x384.png
- icon-512x512.png

## Cara Install PWA di Smartphone

### Android (Chrome)
1. Buka website di Chrome
2. Ketuk menu (⋮) di pojok kanan atas
3. Pilih "Install app" atau "Add to Home screen"
4. Konfirmasi instalasi

### iOS (Safari)
1. Buka website di Safari
2. Ketuk tombol Share (□↑)
3. Scroll dan pilih "Add to Home Screen"
4. Ketuk "Add"

### Desktop (Chrome/Edge)
1. Buka website di browser
2. Klik icon install (⊕) di address bar
3. Konfirmasi instalasi

## Konfigurasi

### Theme Color
Theme color saat ini adalah `#6576ff` (primary color DashLite).
Untuk landing page menggunakan `#0ea5e9` (brand color landing).

### Start URL
Aplikasi akan membuka `/dashboard` saat diluncurkan dari home screen.

## Testing PWA

### Chrome DevTools
1. Buka Developer Tools (F12)
2. Pergi ke tab "Application"
3. Periksa bagian "Manifest" dan "Service Workers"

### Lighthouse Audit
1. Buka Developer Tools (F12)
2. Pergi ke tab "Lighthouse"
3. Pilih "Progressive Web App"
4. Jalankan audit

## Update Cache

Untuk mengupdate cache saat ada perubahan:
1. Update version di `CACHE_NAME` pada `sw.js`
2. Service worker akan otomatis menghapus cache lama

```javascript
// sw.js
const CACHE_NAME = 'jaripos-cache-v2'; // Increment version
```

## Troubleshooting

### Service Worker tidak terdaftar
- Pastikan website diakses via HTTPS atau localhost
- Cek console untuk error

### Icon tidak muncul
- Pastikan path icon benar
- Periksa ukuran icon (minimal 192x192 dan 512x512)

### Manifest error
- Validasi manifest di Chrome DevTools > Application > Manifest

## Resources

- [Web.dev PWA Guide](https://web.dev/progressive-web-apps/)
- [PWA Builder](https://www.pwabuilder.com/)
- [Workbox (Google)](https://developer.chrome.com/docs/workbox/)
