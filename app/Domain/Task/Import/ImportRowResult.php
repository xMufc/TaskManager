<?php

namespace App\Domain\Task\Import;

use App\Domain\Task\Enums\TaskPriority;
use DateTimeImmutable;

final class ImportRowResult
{
    private function __construct(
        public readonly int $row,
        public readonly bool $accepted,
        public readonly ?string $title,
        public readonly ?string $description,
        public readonly ?TaskPriority $priority,
        public readonly ?DateTimeImmutable $dueDate,
        public readonly ?string $reason,
    ) {}

    public static function accepted(int $row, string $title, ?string $description, TaskPriority $priority, ?DateTimeImmutable $dueDate): self
    {
        return new self(
            row: $row,
            accepted: true,
            title: $title,
            description: $description,
            priority: $priority,
            dueDate: $dueDate,
            reason: null,
        );
    }

    public static function rejected(int $row, string $reason): self
    {
        return new self(
            row: $row,
            accepted: false,
            title: null,
            description: null,
            priority: null,
            dueDate: null,
            reason: $reason,
        );
    }
}
