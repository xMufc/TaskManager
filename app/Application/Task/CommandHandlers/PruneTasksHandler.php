<?php

namespace App\Application\Task\CommandHandlers;

use App\Application\Task\Commands\PruneTasksCommand;
use App\Domain\Task\Events\TasksPruned;
use App\Domain\Task\Repositories\TaskRepository;
use DateTimeImmutable;
use Illuminate\Contracts\Events\Dispatcher;
use InvalidArgumentException;

final class PruneTasksHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
        private readonly Dispatcher $events,
    ) {}

    public function handle(PruneTasksCommand $command): int
    {
        if ($command->olderThanDays < 1) {
            throw new InvalidArgumentException('Liczba dni musi być liczbą dodatnią.');
        }

        $threshold = (new DateTimeImmutable)->modify("-{$command->olderThanDays} days");

        $deletedCount = $this->tasks->deleteCreatedBefore($threshold);

        $this->events->dispatch(
            new TasksPruned(
                deletedCount: $deletedCount,
                threshold: $threshold,
            )
        );

        return $deletedCount;
    }
}
