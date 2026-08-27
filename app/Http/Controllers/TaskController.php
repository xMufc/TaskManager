<?php

namespace App\Http\Controllers;

use App\Application\Task\CommandHandlers\CreateTaskHandler;
use App\Application\Task\CommandHandlers\DeleteTaskHandler;
use App\Application\Task\CommandHandlers\UpdateTaskHandler;
use App\Application\Task\CommandHandlers\UpdateTaskStatusHandler;
use App\Application\Task\Commands\CreateTaskCommand;
use App\Application\Task\Commands\DeleteTaskCommand;
use App\Application\Task\Commands\UpdateTaskCommand;
use App\Application\Task\Commands\UpdateTaskStatusCommand;
use App\Application\Task\Data\CreateTaskData;
use App\Application\Task\Data\TaskData;
use App\Application\Task\Data\UpdateTaskData;
use App\Application\Task\Data\UpdateTaskStatusData;
use App\Application\Task\Queries\ListTasksQuery;
use App\Application\Task\QueryHandlers\ListTasksHandler;
use App\Domain\Task\Enums\TaskStatus;
use App\Models\Task as EloquentTask;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class TaskController extends Controller
{
    public function index(Request $request, ListTasksHandler $handler): Response
    {
        $status = $request->query('status')
            ? TaskStatus::from($request->query('status'))
            : null;

        $tasks = $handler->handle(new ListTasksQuery(
            userId: $request->user()->id,
            status: $status,
        ));

        return Inertia::render('Tasks/Index', [
            'tasks' => TaskData::collect($tasks),
            'filters' => ['status' => $status?->value],
        ]);
    }

    public function store(CreateTaskData $data, Request $request, CreateTaskHandler $handler)
    {
        $handler->handle(new CreateTaskCommand(
            userId: $request->user()->id,
            title: $data->title,
            description: $data->description,
            priority: $data->priority,
            dueDate: $data->dueDate ? new DateTimeImmutable($data->dueDate) : null,
        ));

        return back();
    }

    public function update(UpdateTaskData $data, Request $request, EloquentTask $task, UpdateTaskHandler $handler)
    {
        $handler->handle(new UpdateTaskCommand(
            taskId: $task->id,
            userId: $request->user()->id,
            title: $data->title,
            description: $data->description,
            priority: $data->priority,
            dueDate: $data->dueDate ? new DateTimeImmutable($data->dueDate) : null,
        ));

        return back();
    }

    public function changeStatus(UpdateTaskStatusData $data, Request $request, EloquentTask $task, UpdateTaskStatusHandler $handler)
    {
        $handler->handle(new UpdateTaskStatusCommand(
            taskId: $task->id,
            userId: $request->user()->id,
            newStatus: $data->status,
        ));

        return back();
    }

    public function destroy(Request $request, EloquentTask $task, DeleteTaskHandler $handler)
    {
        $handler->handle(new DeleteTaskCommand(
            taskId: $task->id,
            userId: $request->user()->id,
        ));

        return back();
    }
}
