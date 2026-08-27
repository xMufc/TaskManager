<?php

use App\Application\Task\CommandHandlers\PruneTasksHandler;
use App\Application\Task\Commands\PruneTasksCommand;
use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Events\TasksPruned;
use App\Domain\Task\Models\Task;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Tests\Unit\Application\Task\InMemoryTaskRepository;

it('deletes tasks older than the given number of days', function () {
    $repository = new InMemoryTaskRepository;

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')
        ->once()
        ->withArgs(function (TasksPruned $event) {
            expect($event->deletedCount)->toBe(1);

            return true;
        });

    $oldTask = new Task(
        id: 'old-task',
        userId: 'user-1',
        title: 'Old task',
        description: null,
        status: TaskStatus::Todo,
        priority: TaskPriority::Low,
        dueDate: null,
        createdAt: new DateTimeImmutable('-10 days'),
    );

    $recentTask = new Task(
        id: 'recent-task',
        userId: 'user-1',
        title: 'Recent task',
        description: null,
        status: TaskStatus::Todo,
        priority: TaskPriority::Low,
        dueDate: null,
        createdAt: new DateTimeImmutable('-2 days'),
    );

    $repository->save($oldTask);
    $repository->save($recentTask);

    $handler = new PruneTasksHandler($repository, $events);

    $deleted = $handler->handle(new PruneTasksCommand(
        olderThanDays: 7,
    ));

    expect($deleted)->toBe(1);
    expect($repository->find('old-task', 'user-1'))->toBeNull();
    expect($repository->find('recent-task', 'user-1'))->not->toBeNull();
});

it('deletes multiple tasks older than the given number of days', function () {
    $repository = new InMemoryTaskRepository;

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')
        ->once()
        ->withArgs(function (TasksPruned $event) {
            expect($event->deletedCount)->toBe(3);

            return true;
        });

    $tasks = [
        new Task(
            id: 'old-task-1',
            userId: 'user-1',
            title: 'Old task 1',
            description: null,
            status: TaskStatus::Todo,
            priority: TaskPriority::Low,
            dueDate: null,
            createdAt: new DateTimeImmutable('-30 days'),
        ),
        new Task(
            id: 'old-task-2',
            userId: 'user-1',
            title: 'Old task 2',
            description: null,
            status: TaskStatus::Todo,
            priority: TaskPriority::Low,
            dueDate: null,
            createdAt: new DateTimeImmutable('-15 days'),
        ),
        new Task(
            id: 'old-task-3',
            userId: 'user-1',
            title: 'Old task 3',
            description: null,
            status: TaskStatus::Todo,
            priority: TaskPriority::Low,
            dueDate: null,
            createdAt: new DateTimeImmutable('-8 days'),
        ),
    ];

    foreach ($tasks as $task) {
        $repository->save($task);
    }

    $handler = new PruneTasksHandler($repository, $events);

    $deleted = $handler->handle(new PruneTasksCommand(
        olderThanDays: 7,
    ));

    expect($deleted)->toBe(3);
    expect($repository->find('old-task-1', 'user-1'))->toBeNull();
    expect($repository->find('old-task-2', 'user-1'))->toBeNull();
    expect($repository->find('old-task-3', 'user-1'))->toBeNull();
});

it('does not delete tasks newer than the threshold', function () {
    $repository = new InMemoryTaskRepository;

    $events = Mockery::mock(Dispatcher::class);
    $events->shouldReceive('dispatch')
        ->once()
        ->withArgs(function (TasksPruned $event) {
            expect($event->deletedCount)->toBe(0);

            return true;
        });

    $task = new Task(
        id: 'recent-task',
        userId: 'user-1',
        title: 'Recent task',
        description: null,
        status: TaskStatus::Todo,
        priority: TaskPriority::Low,
        dueDate: null,
        createdAt: new DateTimeImmutable('-3 days'),
    );

    $repository->save($task);

    $handler = new PruneTasksHandler($repository, $events);

    $deleted = $handler->handle(new PruneTasksCommand(
        olderThanDays: 7,
    ));

    expect($deleted)->toBe(0);
    expect($repository->find('recent-task', 'user-1'))->not->toBeNull();
});

it('throws an exception when olderThanDays is zero', function () {
    $repository = new InMemoryTaskRepository;

    $events = Mockery::mock(Dispatcher::class);

    $handler = new PruneTasksHandler($repository, $events);

    expect(fn () => $handler->handle(new PruneTasksCommand(
        olderThanDays: 0,
    )))->toThrow(
        InvalidArgumentException::class,
        'Liczba dni musi być liczbą dodatnią.'
    );
});

it('throws an exception when olderThanDays is negative', function () {
    $repository = new InMemoryTaskRepository;

    $events = Mockery::mock(Dispatcher::class);

    $handler = new PruneTasksHandler($repository, $events);

    expect(fn () => $handler->handle(new PruneTasksCommand(
        olderThanDays: -5,
    )))->toThrow(
        InvalidArgumentException::class,
        'Liczba dni musi być liczbą dodatnią.'
    );
});
