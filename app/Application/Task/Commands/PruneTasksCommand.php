<?php

namespace App\Application\Task\Commands;

final class PruneTasksCommand
{
    public function __construct(
        public readonly int $olderThanDays,
    ) {
    }
}
