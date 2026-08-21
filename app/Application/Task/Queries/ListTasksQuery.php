<?php

namespace App\Application\Task\Queries;

use App\Domain\Task\Enums\TaskStatus;

final class ListTasksQuery
{
    public function __construct(
        public readonly string $userId,
        public readonly ?TaskStatus $status = null,
    ) {
    }
}