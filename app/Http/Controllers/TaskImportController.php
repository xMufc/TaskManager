<?php

namespace App\Http\Controllers;

use App\Application\TaskImport\CommandHandlers\ImportTaskHandler;
use App\Application\TaskImport\Commands\ImportTaskCommand;
use App\Application\TaskImport\Queries\GetImportResultQuery;
use App\Application\TaskImport\Queries\ListImportsQuery;
use App\Application\TaskImport\QueryHandlers\GetImportResultHandler;
use App\Application\TaskImport\QueryHandlers\ListImportsHandler;
use App\Models\ImportResult;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class TaskImportController extends Controller
{
    public function index(Request $request, ListImportsHandler $handler): Response
    {
        $imports = $handler->handle(new ListImportsQuery(userId: $request->user()->id));

        return Inertia::render('Imports/Index', ['imports' => $imports]);
    }

    public function store(Request $request, ImportTaskHandler $handler)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);

        $filePath = $request->file('file')->store('imports');

        $importId = $handler->handle(new ImportTaskCommand(userId: $request->user()->id, filePath: $filePath));

        return redirect()->route('imports.show', $importId);
    }

    public function show(Request $request, ImportResult $importResult, GetImportResultHandler $handler): Response
    {
        $importResult = $handler->handle(new GetImportResultQuery(importId: $importResult->id, userId: $request->user()->id));

        return Inertia::render('Imports/Show', [
            'importResult' => [
                'id' => $importResult->id,
                'status' => $importResult->status,
                'accepted' => $importResult->accepted,
                'rejected' => $importResult->rejected,
            ],
        ]);
    }
}
