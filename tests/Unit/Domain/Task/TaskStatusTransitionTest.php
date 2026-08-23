<?php

use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Exceptions\InvalidTaskStatusTransitionException;

it('allows valid status transitions', function (
    TaskStatus $from,
    TaskStatus $to,
) {
    expect($from->canTransitionTo($to))->toBeTrue();
})->with([
    'todo -> in progress' => [
        TaskStatus::Todo,
        TaskStatus::InProgress,
    ],
    'todo -> cancelled' => [
        TaskStatus::Todo,
        TaskStatus::Cancelled,
    ],
    'in progress -> cancelled' => [
        TaskStatus::InProgress,
        TaskStatus::Cancelled,
    ],
    'in progress -> done' => [
        TaskStatus::InProgress,
        TaskStatus::Done,
    ],
    'in progress -> blocked' => [
        TaskStatus::InProgress,
        TaskStatus::Blocked,
    ],
    'blocked -> in progress' => [
        TaskStatus::Blocked,
        TaskStatus::InProgress,
    ],
    'blocked -> cancelled' => [
        TaskStatus::Blocked,
        TaskStatus::Cancelled,
    ],
]);

it('disallows invalid status transitions', function (
    TaskStatus $from,
    TaskStatus $to,
) {
    expect($from->canTransitionTo($to))->toBeFalse();
})->with([
    'todo -> done' => [
        TaskStatus::Todo,
        TaskStatus::Done,
    ],
    'todo -> blocked' => [
        TaskStatus::Todo,
        TaskStatus::Blocked,
    ],
    'in progress -> todo' => [
        TaskStatus::InProgress,
        TaskStatus::Todo,
    ],
    'done -> todo' => [
        TaskStatus::Done,
        TaskStatus::Todo,
    ],
    'done -> in progress' => [
        TaskStatus::Done,
        TaskStatus::InProgress,
    ],
    'done -> cancelled' => [
        TaskStatus::Done,
        TaskStatus::Cancelled,
    ],
    'done -> blocked' => [
        TaskStatus::Done,
        TaskStatus::Blocked,
    ],
    'cancelled -> todo' => [
        TaskStatus::Cancelled,
        TaskStatus::Todo,
    ],
    'cancelled -> in progress' => [
        TaskStatus::Cancelled,
        TaskStatus::InProgress,
    ],
    'cancelled -> done' => [
        TaskStatus::Cancelled,
        TaskStatus::Done,
    ],
    'cancelled -> blocked' => [
        TaskStatus::Cancelled,
        TaskStatus::Blocked,
    ],
    'blocked -> todo' => [
        TaskStatus::Blocked,
        TaskStatus::Todo,
    ],
    'blocked -> done' => [
        TaskStatus::Blocked,
        TaskStatus::Done,
    ],
]);

it('throws exception for invalid status transitions', function (
    TaskStatus $from,
    TaskStatus $to,
) {
    TaskStatus::assertAllowed($from, $to);
})->with([
    'todo -> done' => [
        TaskStatus::Todo,
        TaskStatus::Done,
    ],
    'todo -> blocked' => [
        TaskStatus::Todo,
        TaskStatus::Blocked,
    ],
    'in progress -> todo' => [
        TaskStatus::InProgress,
        TaskStatus::Todo,
    ],
    'done -> todo' => [
        TaskStatus::Done,
        TaskStatus::Todo,
    ],
    'done -> in progress' => [
        TaskStatus::Done,
        TaskStatus::InProgress,
    ],
    'done -> cancelled' => [
        TaskStatus::Done,
        TaskStatus::Cancelled,
    ],
    'done -> blocked' => [
        TaskStatus::Done,
        TaskStatus::Blocked,
    ],
    'cancelled -> todo' => [
        TaskStatus::Cancelled,
        TaskStatus::Todo,
    ],
    'cancelled -> in progress' => [
        TaskStatus::Cancelled,
        TaskStatus::InProgress,
    ],
    'cancelled -> done' => [
        TaskStatus::Cancelled,
        TaskStatus::Done,
    ],
    'cancelled -> blocked' => [
        TaskStatus::Cancelled,
        TaskStatus::Blocked,
    ],
    'blocked -> todo' => [
        TaskStatus::Blocked,
        TaskStatus::Todo,
    ],
    'blocked -> done' => [
        TaskStatus::Blocked,
        TaskStatus::Done,
    ],
])->throws(InvalidTaskStatusTransitionException::class);
