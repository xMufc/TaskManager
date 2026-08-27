<?php

namespace App\Domain\Task\Events;

use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Models\Task;

final class TaskStatusChanged
{
    public function __construct(
        public readonly Task $task,
        public readonly TaskStatus $previousStatus,
    ) {}
}
