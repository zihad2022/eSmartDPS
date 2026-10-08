<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SMART DPS' }} - SMART DPS</title>
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/frontend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/responsive.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/smart-dps.css') }}">
    @stack('styles')
</head>
<body>
<header>
    <a href="{{ route('home') }}"><div class="logo"><i class="fas fa-piggy-bank"></i> SMART DPS</div></a>
    <div class="header-actions">
        <a href="{{ route('client.login') }}" class="icon-btn" aria-label="Login"><i class="fas fa-sign-in-alt"></i></a>
        <a href="{{ route('client.register') }}" class="icon-btn" aria-label="Register"><i class="fas fa-user-plus"></i></a>
    </div>
</header>
{{ $slot }}
<script src="{{ asset('scuser/assets/js/global.min.js') }}"></script>
@vite(['resources/js/app.js'])
@stack('scripts')
</body>
</html>
