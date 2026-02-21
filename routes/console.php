<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily stock balance generation at 00:10 (close yesterday, open today, fix drift)
Schedule::command('stock:generate-daily-balance')->dailyAt('00:10');

// Daily moving status calculation at 00:20 (after stock is reconciled)
Schedule::command('stock:calculate-moving')->dailyAt('00:20');
