<?php

namespace App\Application\Task\CommandHandlers;

use App\Application\Task\Commands\DeleteTaskCommand;
use App\Domain\Task\Repositories\TaskRepository;
use App\Domain\Task\Exceptions\TaskNotFoundException;

final class DeleteTaskHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
    ) {
    }

    public function handle(DeleteTaskCommand $command): void
    {
        $task = $this->tasks->find($command->taskId, $command->userId)
            ?? throw TaskNotFoundException::withId($command->taskId);

        $this->tasks->delete($task->id, $command->userId);
    }
}