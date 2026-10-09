<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Member Portal' }} - SMART DPS</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('smart-dps-theme') || localStorage.getItem('free-sms-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/frontend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/responsive.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/smart-dps.css') }}">
    @stack('styles')
</head>
<body>
<header>
    <a href="{{ route('member.dashboard') }}">
        <div class="logo"><i class="fas fa-piggy-bank"></i> SMART DPS</div>
    </a>
    <div class="header-actions">
        <button type="button" class="icon-btn" id="theme-toggle" aria-label="Toggle light and dark mode" title="Light / Dark mode">
            <i class="fas fa-moon"></i>
        </button>
    </div>
</header>

<main class="container pb-100 smart-client-main smart-member-main">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif
    {{ $slot }}
</main>

<nav class="bottom-nav">
    <a href="{{ route('member.dashboard') }}" class="nav-item"><i class="fas fa-home"></i>Home</a>
    <a href="{{ route('member.payment.create') }}" class="nav-item"><i class="fas fa-credit-card"></i>Payment</a>
    <button type="button" class="nav-item member-nav-button" data-member-logout><i class="fas fa-sign-out-alt"></i>Logout</button>
</nav>

<div class="modal-overlay" id="member-logout-modal" onclick="if(event.target===this) closeModal('member-logout-modal')">
    <div class="modal-content confirm-modal-card">
        <h3 class="modal-title-bold">Confirm Sign Out</h3>
        <p class="modal-desc">Are you sure you want to sign out of the member portal?</p>
        <div class="confirm-modal-actions">
            <button type="button" class="confirm-btn confirm-btn-cancel" onclick="closeModal('member-logout-modal')">Cancel</button>
            <button type="button" class="confirm-btn confirm-btn-primary" onclick="document.getElementById('memberLogoutForm').submit()">Sign out</button>
        </div>
    </div>
</div>
<form id="memberLogoutForm" method="POST" action="{{ route('member.logout') }}" class="hidden">@csrf</form>

<script src="{{ asset('scuser/assets/js/global.min.js') }}"></script>
<script>
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-member-logout]');
        if (!trigger) return;
        event.preventDefault();
        openModal('member-logout-modal');
    });
</script>
@stack('scripts')
</body>
</html>
