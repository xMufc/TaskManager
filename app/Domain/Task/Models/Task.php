<?php

namespace App\Domain\Task\Models;

use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskStatus;
use DateTimeImmutable;

final class Task
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly TaskStatus $status,
        public readonly TaskPriority $priority,
        public readonly ?DateTimeImmutable $dueDate,
    ) {
    }
}