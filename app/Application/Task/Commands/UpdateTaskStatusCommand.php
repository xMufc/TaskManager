<?php

namespace App\Application\Task\Commands;

use App\Domain\Task\Enums\TaskStatus;

final class UpdateTaskStatusCommand
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $userId,
        public readonly TaskStatus $newStatus,
    ) {
    }
}