<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DYDS Savings Software</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        .bg-pattern {
            background-color: #0f172a;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23334155' fill-opacity='0.2'%3E%3Cpath d='M0 38.59l2.83-2.83 1.41 1.41L1.41 40H0v-1.41zM0 1.4l2.83 2.83 1.41-1.41L1.41 0H0v1.41zM38.59 40l-2.83-2.83 1.41-1.41L40 38.59V40h-1.41zM40 1.41l-2.83 2.83-1.41-1.41L38.59 0H40v1.41zM20 18.6l2.83-2.83 1.41 1.41L21.41 20l2.83 2.83-1.41 1.41L20 21.41l-2.83 2.83-1.41-1.41L18.59 20l-2.83-2.83 1.41-1.41L20 18.59z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .float {
            animation: float 3s ease-in-out infinite;
        }

        .login-card {
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="font-sans bg-pattern min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-accent-500 text-white rounded-full mb-4 float">
                <i class="fas fa-shield-alt text-2xl"></i>
            </div>
            <h1 class="text-3xl font-display font-bold text-white mb-2">eSmartDPS</h1>
            <p class="text-primary-300">Smart Savings Management Platform</p>
            {{-- <p class="text-primary-400 text-sm">Admin Access Panel</p> --}}
        </div>

        <!-- Login Form -->
        <div class="glass rounded-2xl p-8 login-card">
            <h2 class="text-2xl font-bold text-white text-center mb-6">Admin Login</h2>
            <form method="POST" action="{{ route('admin.authenticate') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-primary-200 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input type="email" id="email" name="email" required placeholder="Enter your admin email"
                        value="{{ old('email', app()->isLocal() ? 'superadmin@mail.com' : '') }}"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition duration-300"
                        aria-describedby="email-error">
                    @error('email')
                        <p id="email-error" class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-primary-200 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required placeholder="Enter your password"
                            value="{{ app()->isLocal() ? 'password' : '' }}"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition duration-300">
                        <button type="button" id="togglePassword"
                            class="absolute right-3 top-3 text-primary-300 hover:text-white">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" value="1"
                            class="w-4 h-4 text-accent-500 bg-white/10 border-white/20 rounded focus:ring-accent-500">
                        <span class="ml-2 text-sm text-primary-200">Remember me</span>
                    </label>
                    {{-- <a href="#" class="text-sm text-accent-400 hover:text-accent-300 transition duration-300">
                        Forgot password?
                    </a> --}}
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-accent-500 hover:bg-accent-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 focus:ring-offset-primary-900">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>

            <!-- Help Section -->
            {{-- <div class="mt-6 text-center">
                <p class="text-primary-300 text-sm">
                    Having trouble logging in?
                    <a href="mailto:support@esmartdps.com"
                        class="text-accent-400 hover:text-accent-300 font-medium transition duration-300">
                        Contact Support
                    </a>
                </p>
            </div> --}}
        </div>

        <!-- Footer -->
        {{-- <div class="text-center mt-8">
            <p class="text-primary-400 text-sm">
                &copy; {{ date('Y') }} eSmartDPS. All rights reserved.
            </p>
        </div> --}}
    </div>


    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
