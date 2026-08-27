<?php

namespace App\Domain\Task\Events;

final class TaskImportCompleted
{
    public function __construct(
        public readonly string $importResultId,
        public readonly string $userId,
        public readonly int $acceptedCount,
        public readonly int $rejectedCount,
    ) {}
}
