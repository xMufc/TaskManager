<?php

namespace App\Application\Task\CommandHandlers;

use App\Application\Task\Commands\CreateTaskCommand;
use App\Domain\Task\Repositories\TaskRepository;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Models\Task;
use Illuminate\Support\Str;

final class CreateTaskHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
    ) {
    }

    public function handle(CreateTaskCommand $command): Task
    {
        $task = new Task(
            id: (string) Str::uuid(),
            userId: $command->userId,
            title: $command->title,
            description: $command->description,
            status: TaskStatus::Todo,
            priority: $command->priority,
            dueDate: $command->dueDate,
        );

        $this->tasks->save($task);

        event(new TaskCreated($task));

        return $task;
    }
}