<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('invoices:generate')->everyTenSeconds();
Schedule::command('payments:generate')->everyTenSeconds();

Schedule::command('admin:backup')->hourly()->withoutOverlapping();
