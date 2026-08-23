<?php

use App\Application\Task\CommandHandlers\DeleteTaskHandler;
use App\Application\Task\Commands\DeleteTaskCommand;
use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Events\TaskDeleted;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Models\Task;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Tests\Unit\Application\Task\InMemoryTaskRepository;

it('deletes an existing task and dispatches TaskDeleted', function () {
    $repository = new InMemoryTaskRepository();
    $task = new Task(
        id: 'task-1',
        userId: 'user-1',
        title: 'Task to delete',
        description: null,
        status: TaskStatus::Todo,
        priority: TaskPriority::Low,
        dueDate: null,
    );

    $repository->save($task);
    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')
        ->once()
        ->withArgs(function (TaskDeleted $event) use ($task) {
            return $event->task === $task;
        });

    $handler = new DeleteTaskHandler($repository, $events);
    $handler->handle(new DeleteTaskCommand(
        taskId: 'task-1',
        userId: 'user-1',
    ));

    expect($repository->find('task-1', 'user-1'))->toBeNull();
});

it('throws an exception and does not dispatch an event when task does not exist', function () {
    $repository = new InMemoryTaskRepository();
    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->never();

    $handler = new DeleteTaskHandler($repository, $events);

    expect(fn () => $handler->handle(new DeleteTaskCommand(
        taskId: 'missing-task',
        userId: 'user-1',
    )))->toThrow(TaskNotFoundException::class);
});

it('does not delete or dispatch an event for a task belonging to another user', function () {
    $repository = new InMemoryTaskRepository();
    $task = new Task(
        id: 'task-1',
        userId: 'user-1',
        title: 'Private task',
        description: null,
        status: TaskStatus::Todo,
        priority: TaskPriority::Low,
        dueDate: null,
    );
    $repository->save($task);
    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->never();

    $handler = new DeleteTaskHandler($repository, $events);

    expect(fn () => $handler->handle(new DeleteTaskCommand(
        taskId: 'task-1',
        userId: 'user-2',
    )))->toThrow(TaskNotFoundException::class);
    expect($repository->find('task-1', 'user-1'))->not->toBeNull();
});
