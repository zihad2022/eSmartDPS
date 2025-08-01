<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DYDS - Dream Young Development Society</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero-pattern {
            background-image:
                radial-gradient(circle at 25% 25%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(5, 150, 105, 0.1) 0%, transparent 50%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .btn-glow {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }

        .btn-glow:hover {
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="font-sans text-primary-900 scroll-smooth">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="bg-accent-500 text-white p-2 rounded-lg">
                        <i class="fas fa-piggy-bank text-xl"></i>
                    </div>
                    <div>
                        <span class="font-display font-bold text-xl text-primary-900">DYDS</span>
                        <span class="block text-xs text-primary-500">Dream Young Development Society</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-primary-600 hover:text-accent-600 transition duration-300">Home</a>
                    <a href="#about" class="text-primary-600 hover:text-accent-600 transition duration-300">About</a>
                    <a href="#services"
                        class="text-primary-600 hover:text-accent-600 transition duration-300">Services</a>
                    <a href="#contact"
                        class="text-primary-600 hover:text-accent-600 transition duration-300">Contact</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('member.login') }}"
                        class="bg-primary-100 text-primary-700 px-4 py-2 rounded-lg hover:bg-primary-200 transition duration-300">
                        <i class="fas fa-user mr-2"></i>Member Portal
                    </a>
                    <a href="{{ route('client.login') }}"
                        class="bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition duration-300 btn-glow">
                        <i class="fas fa-shield-alt mr-2"></i>Admin Login
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="md:hidden text-primary-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden hidden bg-white border-t border-gray-200">
            <div class="px-4 py-4 space-y-4">
                <a href="#home" class="block text-primary-600 hover:text-accent-600">Home</a>
                <a href="#about" class="block text-primary-600 hover:text-accent-600">About</a>
                <a href="#services" class="block text-primary-600 hover:text-accent-600">Services</a>
                <a href="#contact" class="block text-primary-600 hover:text-accent-600">Contact</a>
                <div class="flex flex-col space-y-2 pt-4 border-t border-gray-200">
                    <a href="{{ route('member.login') }}"
                        class="bg-primary-100 text-primary-700 px-4 py-2 rounded-lg text-left">
                        <i class="fas fa-user mr-2"></i>Member Portal
                    </a>
                    <button onclick="window.location.href='login.html'"
                        class="bg-accent-500 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-shield-alt mr-2"></i>Admin Login
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home"
        class="min-h-screen gradient-bg hero-pattern flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-fade-in-down">
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-bold text-white mb-6">
                    Dream Young<br>
                    <span class="text-accent-200">Development Society</span>
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Empowering communities through collaborative savings and sustainable development projects
                </p>
            </div>

            <div class="animate-fade-in-up flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('member.login') }}"
                    class="bg-white text-accent-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-accent-50 transition duration-300 shadow-lg">
                    <i class="fas fa-user-circle mr-3"></i>Join as Member
                </a>
                <a href="{{ route('client.login') }}"
                    class="bg-accent-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-accent-700 transition duration-300 shadow-lg btn-glow">
                    <i class="fas fa-cog mr-3"></i>Admin Dashboard
                </a>
            </div>

            <div class="mt-16 animate-fade-in-up">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="glass-effect rounded-xl p-6 text-white">
                        <div class="text-3xl font-bold">500+</div>
                        <div class="text-white/80">Active Members</div>
                    </div>
                    <div class="glass-effect rounded-xl p-6 text-white">
                        <div class="text-3xl font-bold">$2.5M</div>
                        <div class="text-white/80">Total Savings</div>
                    </div>
                    <div class="glass-effect rounded-xl p-6 text-white">
                        <div class="text-3xl font-bold">50+</div>
                        <div class="text-white/80">Projects Funded</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 animate-float">
            <div class="w-20 h-20 bg-white/10 rounded-full"></div>
        </div>
        <div class="absolute bottom-20 right-10 animate-float" style="animation-delay: -3s;">
            <div class="w-16 h-16 bg-white/10 rounded-full"></div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-fade-in-up">
                <h2 class="text-4xl md:text-5xl font-display font-bold text-primary-900 mb-6">About DYDS</h2>
                <p class="text-xl text-primary-600 max-w-3xl mx-auto">
                    We are a community-driven organization dedicated to fostering financial inclusion and sustainable
                    development through innovative savings programs.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in-left">
                    <h3 class="text-3xl font-display font-bold text-primary-900 mb-6">Our Mission</h3>
                    <p class="text-lg text-primary-600 mb-6">
                        To empower young individuals and communities by providing accessible financial services,
                        promoting savings culture, and funding development projects that create lasting positive impact.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-accent-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-primary-700">Community-focused savings programs</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-accent-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-primary-700">Transparent financial management</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-accent-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-primary-700">Sustainable development projects</span>
                        </div>
                    </div>
                </div>

                <div class="animate-fade-in-right">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-users text-accent-600 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-primary-900 mb-2">Community</h4>
                                <p class="text-sm text-primary-600">Building stronger communities through collaboration
                                </p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-piggy-bank text-accent-600 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-primary-900 mb-2">Savings</h4>
                                <p class="text-sm text-primary-600">Promoting financial literacy and savings culture
                                </p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-seedling text-accent-600 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-primary-900 mb-2">Growth</h4>
                                <p class="text-sm text-primary-600">Supporting personal and community growth</p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-handshake text-accent-600 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-primary-900 mb-2">Trust</h4>
                                <p class="text-sm text-primary-600">Building trust through transparency and
                                    accountability</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-fade-in-up">
                <h2 class="text-4xl md:text-5xl font-display font-bold text-primary-900 mb-6">Our Services</h2>
                <p class="text-xl text-primary-600 max-w-3xl mx-auto">
                    Comprehensive financial services designed to meet the diverse needs of our community members.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="card-hover bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-coins text-accent-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-primary-900 mb-4">Savings Accounts</h3>
                    <p class="text-primary-600 mb-6">Secure and flexible savings accounts with competitive interest
                        rates and easy access to your funds.</p>
                    <ul class="space-y-2 text-sm text-primary-600">
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>No minimum
                            balance</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Monthly
                            interest payments</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Online account
                            management</li>
                    </ul>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-accent-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-primary-900 mb-4">Investment Plans</h3>
                    <p class="text-primary-600 mb-6">Diversified investment opportunities to help grow your wealth over
                        time with managed risk.</p>
                    <ul class="space-y-2 text-sm text-primary-600">
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Professional
                            management</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Quarterly
                            returns</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Risk assessment
                        </li>
                    </ul>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding-usd text-accent-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-primary-900 mb-4">Micro Loans</h3>
                    <p class="text-primary-600 mb-6">Small business loans and personal loans with flexible repayment
                        terms and competitive rates.</p>
                    <ul class="space-y-2 text-sm text-primary-600">
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Quick approval
                            process</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Flexible
                            repayment</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Business
                            support</li>
                    </ul>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-graduation-cap text-accent-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-primary-900 mb-4">Financial Education</h3>
                    <p class="text-primary-600 mb-6">Comprehensive financial literacy programs to help members make
                        informed financial decisions.</p>
                    <ul class="space-y-2 text-sm text-primary-600">
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Monthly
                            workshops</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Online
                            resources</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Personal
                            consultations</li>
                    </ul>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-project-diagram text-accent-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-primary-900 mb-4">Community Projects</h3>
                    <p class="text-primary-600 mb-6">Collaborative funding for community development projects that
                        benefit all members.</p>
                    <ul class="space-y-2 text-sm text-primary-600">
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Infrastructure
                            development</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Educational
                            initiatives</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Healthcare
                            programs</li>
                    </ul>
                </div>

                <div class="card-hover bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-accent-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-primary-900 mb-4">Digital Banking</h3>
                    <p class="text-primary-600 mb-6">Modern digital banking solutions for convenient and secure
                        financial transactions.</p>
                    <ul class="space-y-2 text-sm text-primary-600">
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Mobile app
                            access</li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>Online payments
                        </li>
                        <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-2"></i>24/7 support
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-fade-in-up">
                <h2 class="text-4xl md:text-5xl font-display font-bold text-primary-900 mb-6">Get In Touch</h2>
                <p class="text-xl text-primary-600 max-w-3xl mx-auto">
                    Ready to start your financial journey with us? Contact our team for more information.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="animate-fade-in-left">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h3 class="text-2xl font-semibold text-primary-900 mb-6">Contact Information</h3>

                        <div class="space-y-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-accent-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-accent-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-primary-900">Address</h4>
                                    <p class="text-primary-600">123 Development Street, Community Center, City 12345
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-accent-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-phone text-accent-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-primary-900">Phone</h4>
                                    <p class="text-primary-600">+1 (555) 123-4567</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-accent-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-envelope text-accent-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-primary-900">Email</h4>
                                    <p class="text-primary-600">info@dyds.org</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-accent-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-accent-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-primary-900">Office Hours</h4>
                                    <p class="text-primary-600">Monday - Friday: 9:00 AM - 5:00 PM</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h4 class="font-semibold text-primary-900 mb-4">Follow Us</h4>
                            <div class="flex space-x-4">
                                <a href="#"
                                    class="w-10 h-10 bg-accent-500 text-white rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 bg-accent-500 text-white rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 bg-accent-500 text-white rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 bg-accent-500 text-white rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="animate-fade-in-right">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h3 class="text-2xl font-semibold text-primary-900 mb-6">Send us a Message</h3>

                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-primary-700 mb-2">First Name</label>
                                    <input type="text"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-primary-700 mb-2">Last Name</label>
                                    <input type="text"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-primary-700 mb-2">Email</label>
                                <input type="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-primary-700 mb-2">Subject</label>
                                <input type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-primary-700 mb-2">Message</label>
                                <textarea rows="5"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-accent-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-accent-600 transition duration-300 btn-glow">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="bg-accent-500 text-white p-2 rounded-lg">
                            <i class="fas fa-piggy-bank text-xl"></i>
                        </div>
                        <div>
                            <span class="font-display font-bold text-xl">DYDS</span>
                            <span class="block text-sm text-gray-300">Dream Young Development Society</span>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-6 max-w-md">
                        Empowering communities through collaborative savings and sustainable development projects. Join
                        us in building a better future together.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="#home"
                                class="text-gray-300 hover:text-accent-400 transition duration-300">Home</a></li>
                        <li><a href="#about"
                                class="text-gray-300 hover:text-accent-400 transition duration-300">About Us</a></li>
                        <li><a href="#services"
                                class="text-gray-300 hover:text-accent-400 transition duration-300">Services</a></li>
                        <li><a href="#contact"
                                class="text-gray-300 hover:text-accent-400 transition duration-300">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-6">Services</h3>
                    <ul class="space-y-3">
                        <li><a href="#"
                                class="text-gray-300 hover:text-accent-400 transition duration-300">Savings
                                Accounts</a></li>
                        <li><a href="#"
                                class="text-gray-300 hover:text-accent-400 transition duration-300">Investment
                                Plans</a></li>
                        <li><a href="#"
                                class="text-gray-300 hover:text-accent-400 transition duration-300">Micro Loans</a>
                        </li>
                        <li><a href="#"
                                class="text-gray-300 hover:text-accent-400 transition duration-300">Financial
                                Education</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-12 pt-8 text-center">
                <p class="text-gray-300">
                    &copy; 2025 DYDS - Dream Young Development Society. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>

</html>
