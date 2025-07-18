<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\RequestResponseLoggerInterface;
use App\Logging\FileRequestResponseLogger;
use App\Logging\DbRequestResponseLogger;

class LoggerServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            RequestResponseLoggerInterface::class,
            FileRequestResponseLogger::class
        );
    }

    public function boot(): void
    {
        //
    }
}
