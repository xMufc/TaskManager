<?php

namespace App\Application\Task\CommandHandlers;

use App\Application\Task\Commands\DeleteTaskCommand;
use App\Domain\Task\Events\TaskDeleted;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepository;
use Illuminate\Contracts\Events\Dispatcher;

final class DeleteTaskHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
        private readonly Dispatcher $events,
    ) {}

    public function handle(DeleteTaskCommand $command): void
    {
        $task = $this->tasks->find($command->taskId, $command->userId)
            ?? throw TaskNotFoundException::withId($command->taskId);

        $this->tasks->delete($task->id, $command->userId);

        $this->events->dispatch(new TaskDeleted($task));
    }
}
