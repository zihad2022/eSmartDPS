@php
$labels=[
 'client.settings.general.edit'=>'General Settings',
 'client.settings.share.edit'=>'Share Settings',
 'client.settings.payment.edit'=>'Payment Settings',
 'client.settings.notification.edit'=>'Notification Settings',
 'client.settings.backup-security.edit'=>'Backup & Security',
];
$title=collect($labels)->first(fn($label,$route)=>request()->routeIs($route)) ?? 'Settings';
@endphp
<x-client.layout.app :title="$title"><section class="page-head"><div><h1>{{ $title }}</h1></div><a class="back-link" href="{{ route('client.settings.index') }}"><i class="fa-solid fa-arrow-left"></i></a></section>{{ $slot }}</x-client.layout.app>