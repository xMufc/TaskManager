<?php

namespace App\Application\Task\Data;

use App\Domain\Task\Enums\TaskPriority;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class CreateTaskData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public readonly string $title,

        #[Nullable]
        public readonly ?string $description,

        #[Required, Enum(TaskPriority::class)]
        public readonly TaskPriority $priority,

        #[Nullable]
        public readonly ?string $dueDate,
    ) {
    }
}