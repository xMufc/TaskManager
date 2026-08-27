<?php

namespace App\Application\TaskImport\QueryHandlers;

use App\Application\TaskImport\Queries\GetImportResultQuery;
use App\Models\ImportResult;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class GetImportResultHandler
{
    public function handle(GetImportResultQuery $query): ImportResult
    {
        $importResult = ImportResult::query()
            ->where('id', $query->importId)
            ->where('user_id', $query->userId)
            ->first();

        if ($importResult === null) {
            throw (new ModelNotFoundException)
                ->setModel(ImportResult::class, [$query->importId]);
        }

        return $importResult;
    }
}
