<?php

namespace App\Domain\Task\Events;

use App\Domain\Task\Models\Task;

final class TaskDeleted
{
    public function __construct(
        public readonly Task $task,
    ) {
    }
}