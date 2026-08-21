<?php

namespace App\Application\Task\CommandHandlers;

use App\Application\Task\Commands\UpdateTaskStatusCommand;
use App\Domain\Task\Repositories\TaskRepository;
use App\Domain\Task\Events\TaskStatusChanged;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Models\Task;
use App\Domain\Task\Rules\TaskStatusTransition;

final class UpdateTaskStatusHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
    ) {
    }

    public function handle(UpdateTaskStatusCommand $command): Task
    {
        $task = $this->tasks->find($command->taskId, $command->userId)
            ?? throw TaskNotFoundException::withId($command->taskId);
            
        TaskStatusTransition::assertAllowed($task->status, $command->newStatus);

        $updated = new Task(
            id: $task->id,
            userId: $task->userId,
            title: $task->title,
            description: $task->description,
            status: $command->newStatus,
            priority: $task->priority,
            dueDate: $task->dueDate,
        );

        $this->tasks->save($updated);

        return $updated;
    }
}