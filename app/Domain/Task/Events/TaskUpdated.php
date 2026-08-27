<?php

namespace App\Domain\Task\Events;

use App\Domain\Task\Models\Task;

final class TaskUpdated
{
    public function __construct(
        public readonly Task $oldTask,
        public readonly Task $newTask,
    ) {}
}
