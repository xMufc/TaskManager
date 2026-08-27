<?php

namespace App\Application\Task\CommandHandlers;

use App\Application\Task\Commands\PruneTasksCommand;
use App\Domain\Task\Repositories\TaskRepository;
use DateTimeImmutable;
use InvalidArgumentException;

final class PruneTasksHandler
{
    public function __construct(
        private readonly TaskRepository $tasks,
    ) {}

    public function handle(PruneTasksCommand $command): int
    {
        if ($command->olderThanDays < 1) {
            throw new InvalidArgumentException('Liczba dni musi być liczbą dodatnią.');
        }

        $threshold = (new DateTimeImmutable)->modify("-{$command->olderThanDays} days");

        return $this->tasks->deleteCreatedBefore($threshold);
    }
}
