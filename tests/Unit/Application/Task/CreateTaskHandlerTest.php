<?php

use App\Application\Task\CommandHandlers\CreateTaskHandler;
use App\Application\Task\Commands\CreateTaskCommand;
use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Events\TaskCreated;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Tests\Unit\Application\Task\InMemoryTaskRepository;

it('creates a task with Todo status regardless of input', function () {
    $repository = new InMemoryTaskRepository;

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')
        ->once()
        ->withArgs(function (TaskCreated $event) {
            expect($event->task->status)->toBe(TaskStatus::Todo);

            return true;
        });

    $handler = new CreateTaskHandler($repository, $events);

    $task = $handler->handle(new CreateTaskCommand(
        userId: 'user-1',
        title: 'Clean the house',
        description: 'Some description',
        priority: TaskPriority::High,
        dueDate: null,
    ));

    expect($task->status)->toBe(TaskStatus::Todo);
    expect($task->title)->toBe('Clean the house');
    expect($task->userId)->toBe('user-1');
});

it('persists the task in the repository', function () {
    $repository = new InMemoryTaskRepository;
    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->once();

    $handler = new CreateTaskHandler($repository, $events);

    $task = $handler->handle(new CreateTaskCommand(
        userId: 'user-1',
        title: 'Clean the house',
        description: null,
        priority: TaskPriority::Low,
        dueDate: null,
    ));

    expect($repository->find($task->id, 'user-1'))->not->toBeNull();
});

it('generates a unique id for each created task', function () {
    $repository = new InMemoryTaskRepository;
    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')->twice();

    $handler = new CreateTaskHandler($repository, $events);

    $task1 = $handler->handle(new CreateTaskCommand('user-1', 'Clean the house', null, TaskPriority::Low, null));
    $task2 = $handler->handle(new CreateTaskCommand('user-1', 'Iron the clothes', null, TaskPriority::Low, null));

    expect($task1->id)->not->toBe($task2->id);
});

it('creates a task with all command data', function () {
    $repository = new InMemoryTaskRepository;
    $events = Mockery::mock(Dispatcher::class);

    $events->shouldReceive('dispatch')->once();

    $handler = new CreateTaskHandler($repository, $events);

    $dueDate = new DateTimeImmutable('2026-10-15 14:30:00');

    $task = $handler->handle(new CreateTaskCommand(
        userId: 'user-42',
        title: 'Iron the clothes',
        description: 'Some description',
        priority: TaskPriority::High,
        dueDate: $dueDate,
    ));

    expect($task->userId)->toBe('user-42');
    expect($task->title)->toBe('Iron the clothes');
    expect($task->description)->toBe('Some description');
    expect($task->priority)->toBe(TaskPriority::High);
    expect($task->dueDate)->toBe($dueDate);
    expect($task->status)->toBe(TaskStatus::Todo);
});
it('creates a task without description', function () {
    $repository = new InMemoryTaskRepository;
    $events = Mockery::mock(Dispatcher::class);

    $events->shouldReceive('dispatch')->once();

    $handler = new CreateTaskHandler($repository, $events);

    $task = $handler->handle(new CreateTaskCommand(
        userId: 'user-1',
        title: 'Task without description',
        description: null,
        priority: TaskPriority::Low,
        dueDate: null,
    ));

    expect($task->description)->toBeNull();
});
