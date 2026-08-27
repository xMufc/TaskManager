<?php

namespace App\Domain\Task\Import;

final class ImportSummary
{
    public function __construct(
        public readonly array $results,
    ) {}

    public function accepted(): array
    {
        return array_values(
            array_filter($this->results, fn (ImportRowResult $result): bool => $result->accepted));
    }

    public function rejected(): array
    {
        return array_values(
            array_filter($this->results, fn (ImportRowResult $result): bool => ! $result->accepted));
    }
}
