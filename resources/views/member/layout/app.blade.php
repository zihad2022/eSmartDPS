<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Portal - DYDS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"> --}}
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }
    </style>
</head>

<body class="font-sans bg-gray-50 text-primary-900">
    <!-- Header -->
    @include('member.layout.partials.navbar')
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>
    <!-- Footer -->
    @include('member.layout.partials.footer')
    {{-- <script>
        // Check if user is logged in
        window.onload = function() {
            const memberId = sessionStorage.getItem('memberId');
            const memberPin = sessionStorage.getItem('memberPin');

            if (!memberId || !memberPin) {
                window.location.href = 'landing.html';
                return;
            }

            // Update member information
            document.getElementById('memberIdDisplay').textContent = `ID: ${memberId}`;
            document.getElementById('welcomeName').textContent = getMemberName(memberId);
            document.getElementById('memberName').textContent = getMemberName(memberId);
        };

        function getMemberName(memberId) {
            // Simulate member data lookup
            const memberData = {
                'M001': 'John Doe',
                'M002': 'Jane Smith',
                'M003': 'Robert Johnson'
            };
            return memberData[memberId] || 'Member';
        }

        function submitPaymentProof() {
            window.location.href = 'payment-proof.html';
        }

        function logout() {
            sessionStorage.removeItem('memberId');
            sessionStorage.removeItem('memberPin');
            window.location.href = 'landing.html';
        }
    </script> --}}
    {{-- Global Confirmation Modal --}}
    <x-confirm-modal />

    @stack('scripts')
</body>

</html>
