<button id="mobileMenuBtn" class="md:hidden text-primary-600">
    <i class="fas fa-bars text-xl"></i>
</button>

   <script>
        // Mobile Menu Toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>