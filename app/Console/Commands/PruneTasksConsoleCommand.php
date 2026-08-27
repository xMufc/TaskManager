<?php

namespace App\Console\Commands;

use App\Application\Task\CommandHandlers\PruneTasksHandler;
use App\Application\Task\Commands\PruneTasksCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use InvalidArgumentException;

#[Signature('tasks:prune {days : Liczba dni - usuń zadania starsze niż podana liczba dni}')]
#[Description('Usuwa zadania utworzone wcześniej niż podana liczba dni temu.')]
class PruneTasksConsoleCommand extends Command
{
    public function handle(PruneTasksHandler $handler): int
    {
        $days = (int) $this->argument('days');

        try {
            $deletedCount = $handler->handle(new PruneTasksCommand($days));
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Usunięto {$deletedCount} zadań starszych niż {$days} dni.");

        return self::SUCCESS;
    }
}
