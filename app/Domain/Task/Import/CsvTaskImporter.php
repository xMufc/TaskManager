<?php

namespace App\Domain\Task\Import;

use App\Domain\Task\Enums\TaskPriority;
use DateTimeImmutable;

final class CsvTaskImporter
{
    private const REQUIRED_HEADERS = [
        'title',
        'priority',
    ];

    public function import(string $csvContent): ImportSummary {
        if (trim($csvContent) === '') {
            return new ImportSummary([]);
        }
        $stream = fopen('php://temp', 'r+');

        if ($stream === false) {
            return new ImportSummary([
                ImportRowResult::rejected(1, 'Nie udało się otworzyć danych CSV.',),
            ]);
        }

        fwrite($stream, $csvContent);
        rewind($stream);

        $header = fgetcsv($stream,0,';','"','\\',);

        if ($header === false) {
            fclose($stream);

            return new ImportSummary([]);
        }

        $header = array_map(static fn ($value): string => trim((string) $value), $header);

        foreach (self::REQUIRED_HEADERS as $required) {
            if (!in_array($required, $header, true)) {
                fclose($stream);

                return new ImportSummary([
                    ImportRowResult::rejected(1, "Brak wymaganej kolumny: {$required}."),
                ]);
            }
        }

        $results = [];
        $rowNumber = 1;

        while (($row = fgetcsv($stream, 0, ';','"', '\\',)) !== false) {
            $rowNumber++;

        if (count(array_filter($row, static fn ($value): bool => trim((string) $value) !== '')) === 0) {
            continue;
        }


            $results[] = $this->processRow($header, $row, $rowNumber);
        }

        fclose($stream);

        return new ImportSummary($results);
    }

    private function processRow(array $header, array $row, int $rowNumber): ImportRowResult {
        if (count($header) !== count($row)) {
            return ImportRowResult::rejected(
                $rowNumber,
                'Nieprawidłowa liczba kolumn.',
            );
        }

        $data = array_combine($header, $row);

        foreach (self::REQUIRED_HEADERS as $required) {
            if (!array_key_exists($required, $data) || trim((string) $data[$required]) === '') {
                $label = match ($required) {
                    'title' => 'tytuł',
                    'priority' => 'priorytet',
                    default => $required,
                };

                return ImportRowResult::rejected(
                    $rowNumber,
                    "Brak wymaganej wartości: {$label}.",
                );
            }
        }

        $title = trim((string) $data['title']);

        if (mb_strlen($title) > 255) {
            return ImportRowResult::rejected(
                $rowNumber,
                'Tytuł jest zbyt długi (max 255 znaków).',
            );
        }

        $priority = TaskPriority::tryFrom(
            trim((string) $data['priority']),
        );

        if ($priority === null) {
            $allowed = implode(', ', array_map(static fn (TaskPriority $case): string => $case->value, TaskPriority::cases()));

            return ImportRowResult::rejected($rowNumber, "Nieprawidłowy priorytet. Dozwolone: {$allowed}.",);
        }

        $description = null;

        if (array_key_exists('description', $data) && trim((string) $data['description']) !== '') {
            $description = trim((string) $data['description']);
        }

        $dueDate = null;

        if (array_key_exists('due_date', $data) && trim((string) $data['due_date']) !== '') {
            $dateString = trim((string) $data['due_date']);

            $dueDate = DateTimeImmutable::createFromFormat('!Y-m-d', $dateString,);

            $dateErrors = DateTimeImmutable::getLastErrors();

            if ($dueDate === false || (is_array($dateErrors) && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))) {
                return ImportRowResult::rejected($rowNumber, 'Nieprawidłowy format daty (oczekiwano YYYY-MM-DD).',);
            }
        }

        return ImportRowResult::accepted(
            row: $rowNumber,
            title: $title,
            description: $description,
            priority: $priority,
            dueDate: $dueDate,
        );
    }
}
