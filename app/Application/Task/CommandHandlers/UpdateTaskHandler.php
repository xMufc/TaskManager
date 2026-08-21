<?php

namespace App\Application\Task\CommandHandlers;

use App\Application\Task\Commands\UpdateTaskCommand;
use App\Domain\Task\Contracts\TaskRepository;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Models\Task;

final class UpdateTaskHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
    ) {
    }

    public function handle(UpdateTaskCommand $command): Task
    {
        $task = $this->tasks->find($command->taskId, $command->userId)
            ?? throw TaskNotFoundException::withId($command->taskId);

        $updated = new Task(
            id: $task->id,
            userId: $task->userId,
            title: $command->title,
            description: $command->description,
            status: $task->status,
            priority: $command->priority,
            dueDate: $command->dueDate,
        );

        $this->tasks->save($updated);

        return $updated;
    }
}