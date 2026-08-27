<?php

namespace App\Domain\Task\Events;

use DateTimeImmutable;

final class TasksPruned
{
    public function __construct(
        public readonly int $deletedCount,
        public readonly DateTimeImmutable $threshold,
    ) {}
}
