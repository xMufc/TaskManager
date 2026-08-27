<?php

namespace App\Domain\Task\Listeners;

use App\Domain\Task\Events\TasksPruned;
use Illuminate\Support\Facades\Log;

final class LogTasksPruned
{
    public function handle(TasksPruned $event): void
    {
        Log::info('Tasks pruned', [
            'deleted_count' => $event->deletedCount,
            'threshold' => $event->threshold->format(DATE_ATOM),
        ]);
    }
}
