<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskStatusChanged;
use App\Domain\Task\Listeners\LogTaskCreated;
use App\Domain\Task\Listeners\LogTaskStatusChanged;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
        App\Domain\Task\Repositories\ITaskRepository::class,
        \App\Infrastructure\Task\EloquentTaskRepository::class,
    );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Event::listen(TaskCreated::class, LogTaskCreated::class);
        Event::listen(TaskStatusChanged::class, LogTaskStatusChanged::class);
    }
}
