<?php

namespace App\Application\TaskImport\Queries;

final readonly class ListImportsQuery
{
    public function __construct(
        public int $userId,
    ) {}
}
