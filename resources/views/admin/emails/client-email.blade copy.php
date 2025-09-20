<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
    <style>
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .button { width: 100% !important; }
        }
        body { margin: 0; padding: 0; background-color: #f4f6f8; font-family: Arial, sans-serif; }
        a { text-decoration: none; }
    </style>
</head>
<body style="background-color:#f4f6f8; margin:0; padding:20px;">

    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <!-- Container -->
                <table width="600" class="container" cellpadding="0" cellspacing="0" style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(90deg,#0ea5e9,#6366f1); padding:20px; text-align:center; color:#ffffff; font-size:22px; font-weight:bold;">
                            Welcome to {{ config('app.name') }}
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#334155; font-size:15px; line-height:1.6;">
                            <h2 style="color:#0f172a; margin-top:0;">Hello {{ $clientName ?? 'User' }},</h2>
                            
                            <p>
                                We’re excited to have you on board 🎉  
                                {{ config('app.name') }} is here to help you achieve your goals with ease and simplicity.
                            </p>

                            <p>
                                To get started, please confirm your account and explore our platform.
                            </p>

                            <!-- Call-to-Action Button -->
                            <div style="text-align:center; margin:25px 0;">
                                <a href="{{ $actionUrl ?? '#' }}" target="_blank" class="button"
                                   style="background:#0ea5e9; color:#ffffff; padding:12px 24px; font-size:15px; font-weight:bold; border-radius:6px; display:inline-block;">
                                    Get Started
                                </a>
                            </div>

                            <p>
                                If you have any questions, just reply to this email — we’d love to help.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8fafc; padding:20px; text-align:center; font-size:13px; color:#64748b;">
                            Thanks,<br>
                            The {{ config('app.name') }} Team <br><br>
                            <small>If you didn’t sign up, you can safely ignore this email.</small>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
