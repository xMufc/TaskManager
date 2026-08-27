<?php

use App\Domain\Task\Enums\TaskPriority;
use App\Domain\Task\Import\CsvTaskImporter;

function makeImporter(): CsvTaskImporter
{
    return new CsvTaskImporter();
}

it('imports all valid rows successfully', function () {
    $importer = makeImporter();

    $csv = "title;description;priority;due_date\n"
        . "Zadanie A;Opis A;high;2026-09-01\n"
        . "Zadanie B;;medium;\n";

    $summary = $importer->import($csv);

    expect($summary->accepted())->toHaveCount(2);
    expect($summary->rejected())->toHaveCount(0);

    expect($summary->accepted()[0]->row)->toBe(2);
    expect($summary->accepted()[0]->title)->toBe('Zadanie A');
    expect($summary->accepted()[0]->description)->toBe('Opis A');
    expect($summary->accepted()[0]->priority)->toBe(TaskPriority::High);
    expect($summary->accepted()[0]->dueDate?->format('Y-m-d'))
        ->toBe('2026-09-01');

    expect($summary->accepted()[1]->row)->toBe(3);
    expect($summary->accepted()[1]->title)->toBe('Zadanie B');
    expect($summary->accepted()[1]->description)->toBeNull();
    expect($summary->accepted()[1]->priority)->toBe(TaskPriority::Medium);
    expect($summary->accepted()[1]->dueDate)->toBeNull();
});

it('rejects invalid rows and reports reasons', function () {
    $importer = makeImporter();

    $csv = "title;description;priority;due_date\n"
        . ";Opis;high;2026-09-01\n"
        . "Zadanie;;invalid;2026-09-01\n"
        . "Zadanie;;medium;not-a-date\n";

    $summary = $importer->import($csv);

    expect($summary->accepted())->toHaveCount(0);
    expect($summary->rejected())->toHaveCount(3);

    expect($summary->rejected()[0]->reason)->toContain('tytuł');
    expect($summary->rejected()[1]->reason)->toContain('priorytet');
    expect($summary->rejected()[2]->reason)->toContain('daty');
});

it('handles partial success', function () {
    $importer = makeImporter();

    $csv = "title;description;priority;due_date\n"
        . "Dobre zadanie;Opis;high;2026-09-01\n"
        . ";Brak tytułu;medium;2026-09-01\n"
        . "Kolejne dobre;;low;\n"
        . "Złe;;invalid;2026-09-01\n";

    $summary = $importer->import($csv);

    expect($summary->accepted())->toHaveCount(2);
    expect($summary->rejected())->toHaveCount(2);

    expect($summary->accepted()[0]->title)->toBe('Dobre zadanie');
    expect($summary->accepted()[1]->title)->toBe('Kolejne dobre');

    expect($summary->rejected()[0]->row)->toBe(3);
    expect($summary->rejected()[1]->row)->toBe(5);
});

it('reports correct row numbers and ignores empty rows', function () {
    $importer = makeImporter();

    $csv = "title;description;priority;due_date\n"
        . "Zadanie;;medium;\n"
        . ";;;\n"
        . "\n"
        . "Drugie zadanie;;high;\n";

    $summary = $importer->import($csv);

    expect($summary->accepted())->toHaveCount(2);
    expect($summary->rejected())->toHaveCount(0);

    expect($summary->accepted()[0]->row)->toBe(2);
    expect($summary->accepted()[1]->row)->toBe(5);
});

it('rejects rows with invalid structure', function () {
    $importer = makeImporter();

    $csv = "title;description;priority;due_date\n"
        . "Zadanie;Opis;medium\n";

    $summary = $importer->import($csv);

    expect($summary->accepted())->toHaveCount(0);
    expect($summary->rejected())->toHaveCount(1);
    expect($summary->rejected()[0]->row)->toBe(2);
    expect($summary->rejected()[0]->reason)->toContain('kolumn');
});

it('rejects CSV without required headers', function () {
    $importer = makeImporter();

    $csv = "description;priority;due_date\n"
        . "Opis;high;2026-09-01\n";

    $summary = $importer->import($csv);

    expect($summary->accepted())->toHaveCount(0);
    expect($summary->rejected())->toHaveCount(1);
    expect($summary->rejected()[0]->reason)->toContain('title');
});

it('rejects title longer than 255 characters', function () {
    $importer = makeImporter();

    $title = str_repeat('A', 256);

    $summary = $importer->import(
        "title;description;priority;due_date\n"
        . "{$title};;medium;\n",
    );

    expect($summary->accepted())->toHaveCount(0);
    expect($summary->rejected())->toHaveCount(1);
    expect($summary->rejected()[0]->reason)->toContain('zbyt długi');
});

it('rejects invalid date formats', function () {
    $importer = makeImporter();

    $csv = "title;description;priority;due_date\n"
        . "Zadanie;;medium;2026-04-31\n"
        . "Zadanie;;medium;2026-09-01 12:00:00\n"
        . "Zadanie;;medium;01-09-2026\n";

    $summary = $importer->import($csv);

    expect($summary->accepted())->toHaveCount(0);
    expect($summary->rejected())->toHaveCount(3);
});

it('handles a large number of rows', function () {
    $importer = makeImporter();

    $csv = "title;description;priority;due_date\n";

    for ($i = 1; $i <= 1000; $i++) {
        $csv .= "Zadanie {$i};;medium;\n";
    }

    $summary = $importer->import($csv);

    expect($summary->accepted())->toHaveCount(1000);
    expect($summary->rejected())->toHaveCount(0);
    expect($summary->accepted()[0]->row)->toBe(2);
    expect($summary->accepted()[999]->row)->toBe(1001);
});
