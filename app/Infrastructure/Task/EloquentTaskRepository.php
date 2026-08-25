<?php

namespace App\Infrastructure\Task;

use App\Domain\Task\Repositories\TaskRepository;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Models\Task as DomainTask;
use App\Models\Task as EloquentTask;

final class EloquentTaskRepository implements TaskRepository
{
    public function find(string $id, string $userId): ?DomainTask
    {
        $model = EloquentTask::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function save(DomainTask $task): void
    {
        EloquentTask::query()->updateOrCreate(
            ['id' => $task->id],
            [
                'user_id' => $task->userId,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->dueDate,
            ],
        );
    }

    public function delete(string $id, string $userId): void
    {
        EloquentTask::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();
    }

    public function allForUser(string $userId, ?TaskStatus $status = null): array
    {
        return EloquentTask::query()
            ->where('user_id', $userId)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->get()
            ->map($this->toDomain(...))
            ->all();
    }

    private function toDomain(EloquentTask $model): DomainTask
    {
        return new DomainTask(
            id: $model->id,
            userId: $model->user_id,
            title: $model->title,
            description: $model->description,
            status: $model->status,
            priority: $model->priority,
            dueDate: $model->due_date?->toDateTimeImmutable(),
        );
    }
}