<?php

namespace App\Application\Task\CommandHandlers;

use App\Application\Task\Commands\UpdateTaskCommand;
use App\Domain\Task\Events\TaskUpdated;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Models\Task;
use App\Domain\Task\Repositories\TaskRepository;
use Illuminate\Contracts\Events\Dispatcher;

final class UpdateTaskHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
        private readonly Dispatcher $events,
    ) {}

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

        $this->events->dispatch(
            new TaskUpdated($task, $updated)
        );

        return $updated;
    }
}
