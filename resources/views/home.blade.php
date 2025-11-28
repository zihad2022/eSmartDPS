<x-app-layout>
    <x-slot:title>Home</x-slot:title>
      <!-- Hero Section -->
      <section id="home"
      class="min-h-screen gradient-bg hero-pattern flex items-center justify-center relative overflow-hidden">
      <div class="absolute inset-0 bg-black"></div>
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
</x-app-layout>
