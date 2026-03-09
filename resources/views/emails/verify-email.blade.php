<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0">
    <title>Verifikasi Email</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        .email-wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .email-card { background: #ffffff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
        .email-header { background: linear-gradient(135deg, #366daf 0%, #02274d 100%); padding: 32px 40px; text-align: center; }
        .email-header h1 { color: #ffffff; font-size: 22px; margin: 0; font-weight: 600; }
        .email-body { padding: 40px; }
        .email-body p { color: #555; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .email-body .greeting { color: #333; font-size: 17px; font-weight: 600; }
        .btn-verify { display: inline-block; background: linear-gradient(135deg, #366daf 0%, #02274d 100%); color: #ffffff !important; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-size: 15px; font-weight: 600; margin: 20px 0; }
        .email-footer { padding: 24px 40px; text-align: center; border-top: 1px solid #eee; }
        .email-footer p { color: #999; font-size: 13px; margin: 0; }
        .link-fallback { word-break: break-all; font-size: 13px; color: #366daf; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <h1>Jari POS</h1>
            </div>
            <div class="email-body">
                <p class="greeting">Halo, {{ $userName }}!</p>
                <p>Terima kasih telah mendaftar di <strong>Jari POS</strong>. Untuk menyelesaikan proses registrasi, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:</p>
                
                <div style="text-align: center;">
                    <a href="{{ $verificationUrl }}" class="btn-verify">Verifikasi Email Saya</a>
                </div>

                <p>Link verifikasi ini akan kedaluwarsa dalam 60 menit. Jika Anda tidak merasa mendaftar di Jari POS, abaikan email ini.</p>

                <p style="margin-top: 24px; font-size: 13px; color: #999;">Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut di browser Anda:</p>
                <p class="link-fallback">{{ $verificationUrl }}</p>
            </div>
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Jari POS. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
