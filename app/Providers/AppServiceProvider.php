<?php

namespace App\Providers;

use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskDeleted;
use App\Domain\Task\Events\TaskImportCompleted;
use App\Domain\Task\Events\TasksPruned;
use App\Domain\Task\Events\TaskStatusChanged;
use App\Domain\Task\Events\TaskUpdated;
use App\Infrastructure\Task\Listeners\LogTaskCreated;
use App\Infrastructure\Task\Listeners\LogTaskDeleted;
use App\Infrastructure\Task\Listeners\LogTaskImportCompleted;
use App\Infrastructure\Task\Listeners\LogTasksPruned;
use App\Infrastructure\Task\Listeners\LogTaskStatusChanged;
use App\Infrastructure\Task\Listeners\LogTaskUpdated;
use App\Domain\Task\Repositories\TaskRepository;
use App\Infrastructure\Task\EloquentTaskRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TaskRepository::class,
            EloquentTaskRepository::class,
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
        Event::listen(TaskUpdated::class, LogTaskUpdated::class);
        Event::listen(TaskDeleted::class, LogTaskDeleted::class);
        Event::listen(TaskImportCompleted::class, LogTaskImportCompleted::class);
        Event::listen(TasksPruned::class, LogTasksPruned::class);

    }
}
