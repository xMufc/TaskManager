<?php

namespace App\Application\Task\Commands;

use App\Domain\Task\Enums\TaskPriority;
use DateTimeImmutable;

final class UpdateTaskCommand
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $userId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly TaskPriority $priority,
        public readonly ?DateTimeImmutable $dueDate,
    ) {
    }
}