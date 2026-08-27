<?php

namespace App\Jobs;

use App\Application\Task\CommandHandlers\CreateTaskHandler;
use App\Application\Task\Commands\CreateTaskCommand;
use App\Domain\Task\Events\TaskImportCompleted;
use App\Domain\Task\Import\CsvTaskImporter;
use App\Domain\Task\Import\ImportRowResult;
use App\Models\ImportResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class ImportTasksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $importResultId,
        public readonly int $userId,
        public readonly string $filePath,
    ) {
    }

    public function handle(CsvTaskImporter $importer,CreateTaskHandler $createTaskHandler): void {
        try {
            $csvContent = Storage::get($this->filePath);

            $summary = $importer->import($csvContent);

            $accepted = [];
            $rejected = $summary->rejected();

            foreach ($summary->accepted() as $result) {
                try {
                    $createTaskHandler->handle(
                        new CreateTaskCommand(
                            userId: $this->userId,
                            title: $result->title,
                            description: $result->description,
                            priority: $result->priority,
                            dueDate: $result->dueDate,
                        ),
                    );

                    $accepted[] = [
                        'row' => $result->row,
                        'title' => $result->title,
                    ];
                } catch (Throwable $exception) {
                    $rejected[] = ImportRowResult::rejected($result->row, 'Błąd zapisu: ' . $exception->getMessage());
                }
            }

            $rejected = array_map(static fn ($result): array => [
                'row' => $result->row,
                'reason' => $result->reason,
            ], $rejected);


            $importResult = ImportResult::findOrFail($this->importResultId);

            $importResult->update([
                'status' => 'completed',
                'accepted' => $accepted,
                'rejected' => $rejected,
            ]);

            event(new TaskImportCompleted(
                importResultId: $importResult->id,
                userId: $this->userId,
                acceptedCount: count($accepted),
                rejectedCount: count($rejected),
            ));
        } finally {
            Storage::delete($this->filePath);
        }
    }

    public function failed(Throwable $exception): void
    {
        ImportResult::query()
            ->whereKey($this->importResultId)
            ->update([
                'status' => 'failed',
            ]);

        Storage::delete($this->filePath);
    }
}
