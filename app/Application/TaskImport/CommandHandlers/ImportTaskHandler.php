<?php

namespace App\Application\TaskImport\CommandHandlers;

use App\Application\TaskImport\Commands\ImportTaskCommand;
use App\Jobs\ImportTasksJob;
use App\Models\ImportResult;
use Illuminate\Support\Str;

final class ImportTaskHandler
{
    public function handle(ImportTaskCommand $command): string
    {
        $importResult = ImportResult::create([
            'id' => (string) Str::uuid(),
            'user_id' => $command->userId,
            'status' => 'processing',
            'accepted' => [],
            'rejected' => [],
        ]);

        ImportTasksJob::dispatch(
            importResultId: $importResult->id,
            userId: $command->userId,
            filePath: $command->filePath,
        );

        return $importResult->id;
    }
}
