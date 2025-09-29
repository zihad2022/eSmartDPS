<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DYDS - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --accent-500: #10b981;
            --accent-600: #059669;
            --primary-900: #111827;
            --primary-700: #374151;
            --primary-600: #4b5563;
            --primary-500: #6b7280;
            --primary-100: #f3f4f6;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--primary-900);
            position: relative;
            overflow-x: hidden;
        }

        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 25% 25%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(5, 150, 105, 0.1) 0%, transparent 50%);
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            animation: fadeIn 0.6s ease-out;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            background-color: var(--accent-500);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
        }

        .logo-text {
            text-align: center;
        }

        .logo-text h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-900);
        }

        .logo-text p {
            font-size: 14px;
            color: var(--accent-600);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary-900);
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--primary-600);
            font-size: 16px;
        }

        .login-form .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-700);
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon input {
            width: 100%;
            padding: 14px 14px 14px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: var(--primary-100);
        }

        .input-with-icon input:focus {
            outline: none;
            border-color: var(--accent-500);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
            background-color: white;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-500);
            font-size: 18px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-500);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            accent-color: var(--accent-500);
        }

        .remember-me label {
            color: var(--primary-600);
            font-size: 14px;
        }

        .forgot-password {
            color: var(--accent-500);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: var(--accent-600);
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background-color: var(--accent-500);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3), 0 2px 4px -1px rgba(16, 185, 129, 0.1);
        }

        .login-button:hover {
            background-color: var(--accent-600);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3), 0 4px 6px -2px rgba(16, 185, 129, 0.1);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .divider {
            position: relative;
            text-align: center;
            margin-bottom: 25px;
            color: var(--primary-500);
        }

        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e5e7eb;
            z-index: 1;
        }

        .divider span {
            position: relative;
            display: inline-block;
            padding: 0 12px;
            background-color: white;
            z-index: 2;
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border: 1px solid #e5e7eb;
            color: var(--primary-600);
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .social-btn.google:hover {
            color: #DB4437;
            border-color: #DB4437;
        }

        .social-btn.facebook:hover {
            color: #4267B2;
            border-color: #4267B2;
        }

        .signup-link {
            text-align: center;
            font-size: 15px;
            color: var(--primary-600);
        }

        .signup-link a {
            color: var(--accent-500);
            font-weight: 600;
            text-decoration: none;
            margin-left: 5px;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            z-index: 1;
        }

        .floating-element:nth-child(1) {
            width: 120px;
            height: 120px;
            top: 10%;
            left: 10%;
            animation: float 8s ease-in-out infinite;
        }

        .floating-element:nth-child(2) {
            width: 80px;
            height: 80px;
            bottom: 15%;
            right: 10%;
            animation: float 10s ease-in-out infinite 1s;
        }

        .floating-element:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 40%;
            right: 20%;
            animation: float 12s ease-in-out infinite 2s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            50% {
                transform: translate(20px, 20px) rotate(180deg);
            }

            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .logo-container {
                flex-direction: column;
                text-align: center;
            }

            .logo-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
 @vite(['resources/css/app.css', 'resources/js/app.js','resources/css/custom-styles.css'])
</head>

<body>
    <!-- Floating background elements -->
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>

    <!-- Background pattern -->
    <div class="hero-pattern"></div>

    <!-- Login Container -->
    <div class="login-container">
        <!-- Logo -->
        <div class="logo-container">
            <div class="logo-icon text-accent-500 text-4xl">
                <i class="fas fa-piggy-bank"></i>
            </div>
        </div>
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <!-- Login Header -->
        <div class="login-header text-center mt-4">
            <h2 class="text-2xl font-bold text-primary-900">Welcome Back</h2>
            <p class="text-sm text-gray-500">Sign in to access your account</p>
        </div>

        <!-- Login Form -->
        <form action="{{ route('member.authenticate') }}" method="POST" class="login-form space-y-4 mt-6">
            @csrf

            <!-- Email Input -->
            <div class="input-group">
                <label for="member_id">Member ID</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope input-icon"></i>
                    <input name="member_id" type="text" id="member_id" placeholder="Enter your member ID" required>
                </div>
            </div>

            <!-- Password Input -->
            <div class="input-group">
                <label for="pin">Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-key input-icon"></i>
                    <input name="password" type="password" id="password" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Remember & Forgot Password -->
            <div class="remember-forgot flex items-center justify-between text-sm">
                <div class="remember-me flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="#" class="text-accent-500 hover:underline">Forgot password?</a>
            </div>

            <!-- Login Button -->
            <button type="submit"
                class="login-button w-full bg-accent-500 text-white py-2 rounded hover:bg-accent-600">
                Sign In
            </button>
        </form>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-accent-500 transition">
                ← Back to Home
            </a>
        </div>
    </div>
</body>

</html>
