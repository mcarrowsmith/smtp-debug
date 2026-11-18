<?php

namespace McArrowsmithPackages\SmtpDebug\Laravel;

use Illuminate\Support\ServiceProvider;

class SmtpDebugServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([SmtpDebugCommand::class]);
        }
    }
}
