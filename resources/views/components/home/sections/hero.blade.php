   <section class="min-h-screen gradient-bg hero-pattern flex items-center justify-center relative overflow-hidden">

       {{-- Background Overlay --}}
       <div class="absolute inset-0 bg-black"></div>

       {{-- Content --}}
       <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

           {{-- Text Content --}}
           <div class="animate-fade-in-down">
               <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-bold text-white mb-6">
                   Dream Young<br>
                   <span class="text-accent-200">Development Society</span>
               </h1>
               <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                   Empowering communities through collaborative savings and sustainable development projects
               </p>
           </div>

           {{-- Buttons --}}
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

           {{-- Stats --}}
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

       {{-- Floating Elements  --}}
       <div class="absolute top-20 left-10 animate-float">
           <div class="w-20 h-20 bg-white/10 rounded-full"></div>
       </div>
       <div class="absolute bottom-20 right-10 animate-float" style="animation-delay: -3s;">
           <div class="w-16 h-16 bg-white/10 rounded-full"></div>
       </div>

   </section>
