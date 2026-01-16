<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Jari POS</title>
    <link rel="shortcut icon" href="{{ asset('images/brand-logo.svg') }}">
    <link rel="manifest" href="/manifest.json">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-container {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .icon-container svg {
            width: 60px;
            height: 60px;
            color: white;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
        }

        p {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #6576ff 0%, #854fff 100%);
            color: white;
            font-size: 16px;
            font-weight: 600;
            padding: 14px 32px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(101, 118, 255, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn svg {
            width: 20px;
            height: 20px;
        }

        .tips {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #eee;
        }

        .tips h3 {
            font-size: 14px;
            color: #888;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .tips ul {
            list-style: none;
            text-align: left;
        }

        .tips li {
            font-size: 14px;
            color: #666;
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tips li::before {
            content: "•";
            color: #6576ff;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
            </svg>
        </div>
        
        <h1>Anda Sedang Offline</h1>
        <p>Sepertinya koneksi internet Anda terputus. Periksa koneksi Anda dan coba lagi.</p>
        
        <button class="btn" onclick="window.location.reload()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Coba Lagi
        </button>

        <div class="tips">
            <h3>Tips</h3>
            <ul>
                <li>Periksa koneksi WiFi atau data seluler Anda</li>
                <li>Coba restart aplikasi atau browser</li>
                <li>Halaman yang sudah dikunjungi tersimpan secara offline</li>
            </ul>
        </div>
    </div>

    <script>
        // Auto reload when back online
        window.addEventListener('online', () => {
            window.location.reload();
        });
    </script>
</body>
</html>
