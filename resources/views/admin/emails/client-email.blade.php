@php
    $settings = \App\Models\AdminSetting::first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; font-family: Arial, sans-serif; }
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .button { width: 100% !important; }
        }
    </style>
</head>
<body style="background-color:#f8fafc; margin:0; padding:20px;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <!-- Email Container -->
                <table class="container" width="600" cellpadding="0" cellspacing="0"
                       style="max-width:600px; background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(90deg,#14b8a6,#6366f1); padding:20px; text-align:center; color:#fff; font-size:22px; font-weight:bold;">
                            {{ $settings->site_name }}
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#334155; font-size:15px; line-height:1.6;">
                            <h2 style="color:#0f172a; margin-top:0;">Hello {{ $client->first_name }},</h2>

                            {{-- The dynamic template body --}}
                            <p>{!! nl2br(e($body)) !!}</p>

                            <div style="margin:25px 0; text-align:center;">
                                <a href="{{ route('client.login') }}" target="_blank"
                                   style="background:#14b8a6; color:#fff; padding:12px 28px; border-radius:8px; font-weight:bold; text-decoration:none; display:inline-block; box-shadow:0 2px 6px rgba(20,184,166,0.4);">
                                    Go to Dashboard
                                </a>
                            </div>

                            <p>If you need any help, just reply to this email — we’re here for you.</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f5f9; padding:20px; text-align:center; font-size:13px; color:#64748b;">
                            Thanks,<br>
                            The {{ $settings->site_name }} Team <br><br>
                            <small>If you did not create this account, please ignore this email.</small>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
