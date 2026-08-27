<?php

namespace App\Domain\Task\Repositories;

use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Models\Task;
use DateTimeImmutable;

interface TaskRepository
{
    public function find(string $id, string $userId): ?Task;

    public function save(Task $task): void;

    public function delete(string $id, string $userId): void;

    public function allForUser(string $userId, ?TaskStatus $status = null): array;
    public function deleteCreatedBefore(DateTimeImmutable $threshold): int;

}
