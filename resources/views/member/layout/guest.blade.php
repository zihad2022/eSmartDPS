<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Member Login' }} - SMART DPS</title>
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
    <a href="{{ route('home') }}"><div class="logo"><i class="fas fa-piggy-bank"></i> SMART DPS</div></a>
    <div class="header-actions public-login-actions">
        <a href="{{ route('client.login') }}" class="portal-login-link"><i class="fas fa-building"></i><span>Client Login</span></a>
        <a href="{{ route('member.login') }}" class="portal-login-link current"><i class="fas fa-user"></i><span>Member Login</span></a>
    </div>
</header>
{{ $slot }}
<script src="{{ asset('scuser/assets/js/global.min.js') }}"></script>
@stack('scripts')
</body>
</html>
