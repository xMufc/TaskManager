<?php

namespace App\Application\Task\QueryHandlers;

use App\Application\Task\Queries\ListTasksQuery;
use App\Domain\Task\Repositories\TaskRepository;

final class ListTasksHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
    ) {}

    public function handle(ListTasksQuery $query): array
    {
        return $this->tasks->allForUser($query->userId, $query->status);
    }
}
