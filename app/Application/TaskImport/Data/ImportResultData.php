<?php

namespace App\Application\TaskImport\Data;

use App\Models\ImportResult;
use Spatie\LaravelData\Data;

final class ImportResultData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly int $acceptedCount,
        public readonly int $rejectedCount,
        public readonly string $createdAt,
    ) {
    }

    public static function fromModel(ImportResult $import): self
    {
        return new self(
            id: $import->id,
            status: $import->status,
            acceptedCount: count($import->accepted),
            rejectedCount: count($import->rejected),
            createdAt: $import->created_at->format('Y-m-d H:i'),
        );
    }
}
