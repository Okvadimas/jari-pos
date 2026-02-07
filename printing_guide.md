# Panduan Setting Direct Print (Tanpa Dialog)

Web browser secara default **memblokir** pencetakan otomatis (silent printing) demi keamanan, sehingga dialog print preview selalu muncul.

Untuk membuat printer langsung mencetak tanpa dialog popup ("langsung print"), Anda perlu mengatur browser ke **Kiosk Printing Mode**.

## Cara Setting untuk Chrome / Edge (Windows)

1.  **Buat Shortcut Baru**:
    *   Klik kanan pada Desktop -> New -> Shortcut.
    *   Browse ke lokasi `chrome.exe` atau `msedge.exe`.
    *   Biasanya ada di:
        *   Chrome: `C:\Program Files\Google\Chrome\Application\chrome.exe`
        *   Edge: `C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe`
    *   Klik Next, beri nama "JariPOS Kiosk" -> Finish.

2.  **Edit Target Shortcut**:
    *   Klik kanan pada shortcut baru tersebut -> **Properties**.
    *   Di kolom **Target**, tambahkan spasi di paling akhir, lalu copas parameter berikut:
        `--kiosk-printing`
    *   Contoh hasil akhirnya:
        `"C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk-printing`

3.  **Matikan Browser Sepenuhnya**:
    *   Pastikan **semua** jendela Chrome/Edge tertutup (cek di Task Manager jika perlu). Setting ini baru aktif saat browser dijalankan pertama kali dari shortcut tersebut.

4.  **Jalankan POS**:
    *   Buka shortcut "JariPOS Kiosk" tadi.
    *   Coba lakukan transaksi atau Test Receipt.
    *   Dialog print akan muncul sekejap lalu hilang dan langsung nge-print ke **Default Printer**.

## Pastikan Default Printer Benar
Karena Kiosk Mode tidak membiarkan Anda memilih printer, pastikan printer thermal Anda sudah diset sebagai **Default Printer** di Windows:
1.  Buka **Settings** -> **Bluetooth & devices** -> **Printers & scanners**.
2.  Pilih printer thermal Anda.
3.  Klik **Set as default**.

## Catatan Tambahan
*   **Auto-Print Script**: Saya telah menghapus script auto-print dari dalam file receipt (`receipt.blade.php`) agar tidak bentrok conflict dengan sistem POS utama. Sekarang kontrol print sepenuhnya ada di tombol "Print Receipt" atau otomatis setelah bayar (jika diaktifkan di setting POS).
