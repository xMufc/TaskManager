<?php

namespace App\Application\Task\QueryHandlers;

use App\Domain\Task\Repositories\TaskRepository;
use App\Domain\Task\Models\Task;

final class ListTasksHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
    ) {
    }

    public function handle(ListTasksQuery $query): array
    {
        return $this->tasks->allForUser($query->userId, $query->status);
    }
}