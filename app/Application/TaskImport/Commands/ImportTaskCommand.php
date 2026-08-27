<?php

namespace App\Application\TaskImport\Commands;

final readonly class ImportTaskCommand
{
    public function __construct(
        public int $userId,
        public string $filePath,
    ) {}
}
