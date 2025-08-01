<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DYDS Savings Software</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        },
                        accent: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>
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
                <i class="fas fa-piggy-bank text-2xl"></i>
            </div>
            <h1 class="text-3xl font-display font-bold text-white mb-2">DYDS</h1>
            <p class="text-primary-300">Dream Young Development Society</p>
            <p class="text-primary-400 text-sm">Savings Management System</p>
        </div>

        <!-- Login Form -->
        <div class="glass rounded-2xl p-8 login-card">
            <h2 class="text-2xl font-bold text-white text-center mb-6">Welcome Back</h2>
            <!-- Error Message -->
            @if (session('error'))
                <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif
            <form method="POST" action="{{ route('client.authenticate') }}" id="loginForm" class="space-y-6">
                @csrf

                <!-- Username Field -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-primary-200 mb-2">
                        <i class="fas fa-user mr-2"></i>User ID
                    </label>
                    <input type="text" id="user_id" name="user_id" required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition duration-300"
                        placeholder="Enter your user ID" value="UID01">
                    {{-- display error messages --}}
                    @error('user_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-primary-200 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition duration-300"
                            placeholder="Enter your password" value="owner1234">
                        <button type="button" id="togglePassword"
                            class="absolute right-3 top-3 text-primary-300 hover:text-white">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 text-accent-500 bg-white/10 border-white/20 rounded focus:ring-accent-500">
                        <span class="ml-2 text-sm text-primary-200">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-accent-400 hover:text-accent-300 transition duration-300">
                        Forgot password?
                    </a>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full bg-accent-500 hover:bg-accent-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 focus:ring-offset-primary-900">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>


            <!-- Additional Options -->
            <div class="mt-6 text-center">
                <p class="text-primary-300 text-sm">
                    Don't have an account?
                    <a href="#" class="text-accent-400 hover:text-accent-300 font-medium transition duration-300">
                        Contact Administrator
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-primary-400 text-sm">
                &copy; 2025 DYDS Savings Software. All rights reserved.
            </p>
        </div>
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
