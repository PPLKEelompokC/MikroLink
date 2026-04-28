<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('fund-allocation:analyze')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->description('Analyze idle funds and generate AI allocation recommendations');
