<?php

namespace App\Application\Task\Commands;

final class DeleteTaskCommand
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $userId,
    ) {}
}
