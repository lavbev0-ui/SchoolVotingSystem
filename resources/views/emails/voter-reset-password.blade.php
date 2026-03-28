<div>
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
</div>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background-color: #1d4ed8; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; }
        .header p { color: #bfdbfe; margin: 5px 0 0; font-size: 13px; }
        .body { padding: 35px 30px; }
        .body p { color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 15px; }
        .btn { display: block; width: fit-content; margin: 25px auto; padding: 14px 32px; background-color: #1d4ed8; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 15px; text-align: center; }
        .note { background: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 12px 16px; border-radius: 4px; margin: 20px 0; }
        .note p { color: #0369a1; font-size: 13px; margin: 0; }
        .url-box { background: #f3f4f6; padding: 12px; border-radius: 6px; word-break: break-all; font-size: 12px; color: #6b7280; margin: 10px 0; }
        .footer { background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset</h1>
            <p>Enhance Voting System — Catalino D. Cerezo NHS</p>
        </div>

        <div class="body">
            <p>Kumusta, <strong>{{ $voter->first_name }}</strong>!</p>

            <p>Nakatanggap kami ng request para i-reset ang password ng iyong student account sa <strong>Enhance Voting System</strong>.</p>

            <p>I-click ang button sa ibaba para mag-set ng bagong password:</p>

            <a href="{{ $resetUrl }}" class="btn">I-reset ang Password</a>

            <div class="note">
                <p>⏰ <strong>Mahalaga:</strong> Ang reset link ay mag-e-expire pagkatapos ng <strong>60 minuto</strong>.</p>
            </div>

            <p>Kung hindi ka nag-request ng password reset, huwag pansinin ang email na ito at mananatiling ligtas ang iyong account.</p>

            <p style="font-size: 13px; color: #6b7280;">Kung hindi gumagana ang button, kopyahin at i-paste ang link na ito sa iyong browser:</p>
            <div class="url-box">{{ $resetUrl }}</div>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Enhance Voting System — Catalino D. Cerezo National High School</p>
            <p style="margin-top: 5px;">Huwag sagutin ang email na ito.</p>
        </div>
    </div>
</body>
</html>