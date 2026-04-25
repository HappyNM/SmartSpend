<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('expense-generate-recurring-expense')
->daily('00:00')
->withoutOverlapping()
->onSuccess(function(){
    Log::info("Recurring expense generated successfully");
})
->onFailure(function () {
    Log::info("Failed to generate recurring expense");
});