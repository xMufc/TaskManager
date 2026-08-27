<?php

namespace App\Domain\Task\Listeners;

use App\Domain\Task\Events\TaskImportCompleted;
use Illuminate\Support\Facades\Log;

final class LogTaskImportCompleted
{
    public function handle(TaskImportCompleted $event): void
    {
        Log::info('Task import completed', [
            'import_result_id' => $event->importResultId,
            'user_id' => $event->userId,
            'accepted' => $event->acceptedCount,
            'rejected' => $event->rejectedCount,
        ]);
    }
}
