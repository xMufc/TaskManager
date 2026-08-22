<?php

namespace App\Application\Task\Data;

use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Models\Task;
use Spatie\LaravelData\Data;

final class TaskData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly TaskStatus $status,
        public readonly TaskPriority $priority,
        public readonly ?string $dueDate,
        public readonly array $allowedNextStatuses,
    ) {
    }

    public static function fromDomain(Task $task): self
    {
        return new self(
            id: $task->id,
            title: $task->title,
            description: $task->description,
            status: $task->status,
            priority: $task->priority,
            dueDate: $task->dueDate?->format('Y-m-d'),
            allowedNextStatuses: \App\Domain\Task\Rules\TaskStatusTransition::allowedNextStatuses($task->status),
        );
    }
}