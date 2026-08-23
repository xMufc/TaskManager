<?php

use App\Application\Task\CommandHandlers\UpdateTaskHandler;
use App\Application\Task\Commands\UpdateTaskCommand;
use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Events\TaskUpdated;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Models\Task;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Tests\Unit\Application\Task\InMemoryTaskRepository;

function makeUpdateTask(): Task
{
    return new Task(
        id: 'task-1',
        userId: 'user-1',
        title: 'Old title',
        description: 'Old description',
        status: TaskStatus::InProgress,
        priority: TaskPriority::Low,
        dueDate: new DateTimeImmutable('2026-09-01 12:00:00'),
    );
}

it('updates task data', function () {
    $repository = new InMemoryTaskRepository();
    $task = makeUpdateTask();

    $repository->save($task);

    $events = Mockery::mock(Dispatcher::class);

    $events->shouldReceive('dispatch')
        ->once()
        ->withArgs(function (TaskUpdated $event) {
            return $event->newTask->title === 'New title'
                && $event->newTask->description === 'New description'
                && $event->newTask->priority === TaskPriority::High
                && $event->newTask->dueDate->format('Y-m-d H:i:s') === '2026-10-15 14:30:00';
        });

    $handler = new UpdateTaskHandler($repository, $events);

    $updated = $handler->handle(new UpdateTaskCommand(
        taskId: 'task-1',
        userId: 'user-1',
        title: 'New title',
        description: 'New description',
        priority: TaskPriority::High,
        dueDate: new DateTimeImmutable('2026-10-15 14:30:00'),
    ));

    expect($updated->title)->toBe('New title');
    expect($updated->description)->toBe('New description');
    expect($updated->priority)->toBe(TaskPriority::High);
    expect($updated->dueDate)->toEqual(new DateTimeImmutable('2026-10-15 14:30:00'));
});

it('preserves task identity and status', function () {
    $repository = new InMemoryTaskRepository();
    $task = makeUpdateTask();

    $repository->save($task);

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->once();

    $handler = new UpdateTaskHandler($repository, $events);

    $updated = $handler->handle(new UpdateTaskCommand(
        taskId: 'task-1',
        userId: 'user-1',
        title: 'New title',
        description: null,
        priority: TaskPriority::High,
        dueDate: null,
    ));

    expect($updated->id)->toBe('task-1');
    expect($updated->userId)->toBe('user-1');
    expect($updated->status)->toBe(TaskStatus::InProgress);
});

it('dispatches TaskUpdated with old and new task', function () {
    $repository = new InMemoryTaskRepository();
    $task = makeUpdateTask();

    $repository->save($task);

    $events = Mockery::mock(Dispatcher::class);

    $events->shouldReceive('dispatch')
        ->once()
        ->withArgs(function (TaskUpdated $event) use ($task) {
            return $event->oldTask === $task
                && $event->newTask->id === $task->id
                && $event->newTask->title === 'New title';
        });

    $handler = new UpdateTaskHandler($repository, $events);

    $handler->handle(new UpdateTaskCommand(
        taskId: 'task-1',
        userId: 'user-1',
        title: 'New title',
        description: 'New description',
        priority: TaskPriority::High,
        dueDate: null,
    ));
});

it('throws not found when task does not exist', function () {
    $repository = new InMemoryTaskRepository();

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->never();

    $handler = new UpdateTaskHandler($repository, $events);

    expect(fn () => $handler->handle(new UpdateTaskCommand(
        taskId: 'missing-task',
        userId: 'user-1',
        title: 'New title',
        description: null,
        priority: TaskPriority::High,
        dueDate: null,
    )))->toThrow(TaskNotFoundException::class);
});

it('throws not found when task belongs to another user', function () {
    $repository = new InMemoryTaskRepository();
    $repository->save(makeUpdateTask());

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->never();

    $handler = new UpdateTaskHandler($repository, $events);

    expect(fn () => $handler->handle(new UpdateTaskCommand(
        taskId: 'task-1',
        userId: 'someone-else',
        title: 'Hacked title',
        description: 'Hacked description',
        priority: TaskPriority::High,
        dueDate: null,
    )))->toThrow(TaskNotFoundException::class);

    $unchanged = $repository->find('task-1', 'user-1');

    expect($unchanged->title)->toBe('Old title');
    expect($unchanged->description)->toBe('Old description');
    expect($unchanged->priority)->toBe(TaskPriority::Low);
});