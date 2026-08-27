<?php

namespace App\Application\TaskImport\Queries;

final readonly class GetImportResultQuery
{
    public function __construct(
        public string $importId,
        public int $userId,
    ) {
    }
}
