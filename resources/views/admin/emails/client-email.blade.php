<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f9f0 0%, #e6f4e6 100%);
            padding: 20px;
            color: #2c3e50;
            line-height: 1.6;
        }

        .container {
            max-width: 700px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 24px rgba(30, 100, 50, 0.15);
            position: relative;
        }

        .header {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            padding: 40px 30px 30px;
            color: white;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #16a085, #27ae60, #2ecc71);
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .logo i {
            margin-right: 10px;
            font-size: 32px;
            vertical-align: middle;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .welcome-text {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 25px;
            opacity: 0.95;
        }

        .content {
            padding: 40px 30px;
        }

        .section {
            margin-bottom: 35px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        h2 {
            color: #27ae60;
            font-size: 22px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e0f2e9;
            display: flex;
            align-items: center;
        }

        h2 i {
            margin-right: 12px;
            background: #e8f5e9;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #27ae60;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            display: flex;
            margin-bottom: 16px;
        }

        .label {
            flex: 0 0 120px;
            font-weight: 600;
            color: #555;
        }

        .value {
            flex: 1;
            font-weight: 500;
            color: #2c3e50;
        }

        .highlight {
            color: #27ae60;
            font-weight: 600;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #d0e8d0, transparent);
            margin: 30px 0;
        }

        .footer {
            background: #f8fcf8;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e0f2e9;
        }

        .celebration {
            background: #e8f5e9;
            border-radius: 50px;
            padding: 15px 25px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            color: #27ae60;
            margin-top: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .celebration i {
            font-size: 24px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .contact {
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }

        .contact a {
            color: #27ae60;
            text-decoration: none;
            font-weight: 500;
        }

        .contact a:hover {
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }

        .badge-active {
            background: #e8f5e9;
            color: #27ae60;
        }

        .badge-inactive {
            background: #fde8e8;
            color: #e74c3c;
        }

        @media (max-width: 600px) {
            .container {
                margin: 15px auto;
                border-radius: 12px;
            }

            .header {
                padding: 30px 20px;
            }

            .content {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .welcome-text {
                font-size: 16px;
            }

            h2 {
                font-size: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-leaf"></i>GreenPlatform
            </div>
            <h1>Welcome, {{ $client->first_name }} {{ $client->last_name }}!</h1>
            <p class="welcome-text">Your account has been successfully created. Here are your account details:</p>
        </div>

        <div class="content">
            <div class="section">
                <h2><i class="fas fa-user-circle"></i> Account Details</h2>
                <div class="info-grid">
                    <div>
                        <div class="info-item">
                            <span class="label">User ID:</span>
                            <span class="value highlight">{{ $client->user_id }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Email:</span>
                            <span class="value">{{ $client->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Phone:</span>
                            <span class="value">{{ $client->phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="info-item">
                            <span class="label">Role:</span>
                            <span class="value highlight">{{ ucfirst($client->role) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Status:</span>
                            <span class="value">
                                {{ $client->status ? 'Active' : 'Inactive' }}
                                <span class="badge {{ $client->status ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $client->status ? '✓ Active' : '✗ Inactive' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">NID Number:</span>
                            <span class="value">{{ $client->nid_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="section">
                <h2><i class="fas fa-map-marker-alt"></i> Address Details</h2>
                <div class="info-grid">
                    <div>
                        <div class="info-item">
                            <span class="label">Division:</span>
                            <span class="value">{{ $client->division ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">District:</span>
                            <span class="value">{{ $client->district ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="info-item">
                            <span class="label">Address:</span>
                            <span class="value">{{ $client->address ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Postal Code:</span>
                            <span class="value">{{ $client->postal_code ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="celebration">
                <i class="fas fa-glass-cheers"></i>
                We're excited to have you with us! 🎉
            </div>
            <div class="contact">
                Need assistance? Contact our support team at <a
                    href="mailto:support@greenplatform.com">support@greenplatform.com</a>
            </div>
        </div>
    </div>
</body>

</html>
