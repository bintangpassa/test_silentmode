<?php

namespace App\Providers;

use App\Listeners\TenantSocketEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Reverb\Events\MessageReceived;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MessageReceived::class => [
            TenantSocketEventListener::class,
        ],
    ];
}
