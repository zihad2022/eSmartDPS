<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('invoices:generate')->hourly()->withoutOverlapping();
Schedule::command('saas:generate-upcoming-invoices')->daily()->withoutOverlapping();
Schedule::command('payments:generate')->daily()->withoutOverlapping();
Schedule::command('admin:backup')->hourly()->withoutOverlapping();
