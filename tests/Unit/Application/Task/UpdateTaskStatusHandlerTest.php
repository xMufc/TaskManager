<?php

use App\Application\Task\CommandHandlers\UpdateTaskStatusHandler;
use App\Application\Task\Commands\UpdateTaskStatusCommand;
use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Events\TaskStatusChanged;
use App\Domain\Task\Exceptions\InvalidTaskStatusTransitionException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Models\Task;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Tests\Unit\Application\Task\InMemoryTaskRepository;

function makeTask(TaskStatus $status = TaskStatus::Todo): Task
{
    return new Task(
        id: 'task-1',
        userId: 'user-1',
        title: 'Test task',
        description: null,
        status: $status,
        priority: TaskPriority::Medium,
        dueDate: null,
    );
}

it('changes status when transition is allowed', function () {
    $repository = new InMemoryTaskRepository;
    $repository->save(makeTask(TaskStatus::Todo));

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')
        ->once()
        ->withArgs(function (TaskStatusChanged $event) {
            return $event->task->status === TaskStatus::InProgress;
        });

    $handler = new UpdateTaskStatusHandler($repository, $events);

    $updated = $handler->handle(new UpdateTaskStatusCommand(
        taskId: 'task-1',
        userId: 'user-1',
        newStatus: TaskStatus::InProgress,
    ));

    expect($updated->status)->toBe(TaskStatus::InProgress);
    expect($repository->find('task-1', 'user-1')->status)->toBe(TaskStatus::InProgress);
});

it('throws when transition is not allowed', function () {
    $repository = new InMemoryTaskRepository;
    $repository->save(makeTask(TaskStatus::Done));

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->never();

    $handler = new UpdateTaskStatusHandler($repository, $events);

    expect(fn () => $handler->handle(new UpdateTaskStatusCommand(
        taskId: 'task-1',
        userId: 'user-1',
        newStatus: TaskStatus::InProgress,
    )))->toThrow(InvalidTaskStatusTransitionException::class);

    expect($repository->find('task-1', 'user-1')->status)->toBe(TaskStatus::Done);
});

it('throws not found when task belongs to another user', function () {
    $repository = new InMemoryTaskRepository;
    $repository->save(makeTask(TaskStatus::Todo));

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->never();

    $handler = new UpdateTaskStatusHandler($repository, $events);

    expect(fn () => $handler->handle(new UpdateTaskStatusCommand(
        taskId: 'task-1',
        userId: 'someone-else',
        newStatus: TaskStatus::InProgress,
    )))->toThrow(TaskNotFoundException::class);
});
