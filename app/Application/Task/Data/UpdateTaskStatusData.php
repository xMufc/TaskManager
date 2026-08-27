<?php

namespace App\Application\Task\Data;

use App\Domain\Task\Enums\TaskStatus;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class UpdateTaskStatusData extends Data
{
    public function __construct(
        #[Required, Enum(TaskStatus::class)]
        public readonly TaskStatus $status,
    ) {}
}
