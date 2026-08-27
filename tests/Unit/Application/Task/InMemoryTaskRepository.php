<?php

namespace Tests\Unit\Application\Task;

use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Models\Task;
use App\Domain\Task\Repositories\TaskRepository;
use DateTimeImmutable;

final class InMemoryTaskRepository implements TaskRepository
{
    private array $tasks = [];

    public function find(string $id, string $userId): ?Task
    {
        $task = $this->tasks[$id] ?? null;

        return ($task && $task->userId === $userId) ? $task : null;
    }

    public function save(Task $task): void
    {
        $this->tasks[$task->id] = $task;
    }

    public function delete(string $id, string $userId): void
    {
        unset($this->tasks[$id]);
    }

    public function allForUser(string $userId, ?TaskStatus $status = null): array
    {
        return array_values(array_filter(
            $this->tasks,
            fn (Task $t) => $t->userId === $userId && ($status === null || $t->status === $status),
        ));
    }

    public function deleteCreatedBefore(DateTimeImmutable $threshold): int
    {
        $toDelete = array_filter(
            $this->tasks,
            fn (Task $t) => $t->createdAt < $threshold,
        );

        foreach ($toDelete as $id => $task) {
            unset($this->tasks[$id]);
        }

        return count($toDelete);
    }
}
