<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0">
    <title>Reset Kata Sandi</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        .email-wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .email-card { background: #ffffff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
        .email-header { background: linear-gradient(135deg, #366daf 0%, #02274d 100%); padding: 32px 40px; text-align: center; }
        .email-header h1 { color: #ffffff; font-size: 22px; margin: 0; font-weight: 600; }
        .email-body { padding: 40px; }
        .email-body p { color: #555; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .email-body .greeting { color: #333; font-size: 17px; font-weight: 600; }
        .password-box { background: #f8f9fa; border: 2px dashed #366daf; border-radius: 8px; padding: 16px 24px; text-align: center; margin: 20px 0; }
        .password-box .label { font-size: 13px; color: #999; margin: 0 0 4px; }
        .password-box .password { font-size: 24px; font-weight: 700; color: #02274d; letter-spacing: 3px; margin: 0; font-family: monospace; }
        .email-footer { padding: 24px 40px; text-align: center; border-top: 1px solid #eee; }
        .email-footer p { color: #999; font-size: 13px; margin: 0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <h1>Jari POS</h1>
            </div>
            <div class="email-body">
                <p class="greeting">Halo, {{ $user->name }}!</p>
                <p>Kata sandi akun Jari POS Anda telah direset. Berikut adalah kata sandi baru Anda:</p>
                
                <div class="password-box">
                    <p class="label">Kata Sandi Baru</p>
                    <p class="password">{{ $newPassword }}</p>
                </div>

                <p>Segera login dan ubah kata sandi Anda demi keamanan akun. Jika Anda tidak meminta reset kata sandi, segera hubungi administrator.</p>
            </div>
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Jari POS. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
