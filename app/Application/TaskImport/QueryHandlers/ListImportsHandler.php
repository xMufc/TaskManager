<?php

namespace App\Application\TaskImport\QueryHandlers;

use App\Application\TaskImport\Data\ImportResultData;
use App\Application\TaskImport\Queries\ListImportsQuery;
use App\Models\ImportResult;
use Illuminate\Support\Collection;

final class ListImportsHandler
{

    public function handle(ListImportsQuery $query): Collection
    {
        return ImportResult::query()
            ->where('user_id', $query->userId)
            ->latest()
            ->get()
            ->map(
                static fn (ImportResult $import): ImportResultData =>
                    ImportResultData::fromModel($import),
            );
    }
}
