<?php

namespace Tests\Unit\Domain\Task;

use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Exceptions\InvalidTaskStatusTransitionException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaskStatusTransitionTest extends TestCase
{
    #[DataProvider('allowedTransitions')]
    public function test_allows_transition(
        TaskStatus $from,
        TaskStatus $to,
    ): void {
        $this->assertTrue($from->canTransitionTo($to));
    }

    #[DataProvider('disallowedTransitions')]
    public function test_disallows_transition(
        TaskStatus $from,
        TaskStatus $to,
    ): void {
        $this->assertFalse($from->canTransitionTo($to));
    }

    #[DataProvider('disallowedTransitions')]
    public function test_throws_exception_for_disallows_transition(
        TaskStatus $from,
        TaskStatus $to,
    ): void {
        $this->expectException(InvalidTaskStatusTransitionException::class);
        TaskStatus::assertAllowed($from,$to);
    }

    public static function allowedTransitions(): array
    {
        return [
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
        ];
    }

    public static function disallowedTransitions(): array
    {
        return [
            'todo -> done' => [TaskStatus::Todo, TaskStatus::Done],
            'todo -> blocked' => [TaskStatus::Todo, TaskStatus::Blocked],

            'in progress -> todo' => [
                TaskStatus::InProgress,
                TaskStatus::Todo,
            ],

            'done -> todo' => [TaskStatus::Done, TaskStatus::Todo],
            'done -> in progress' => [TaskStatus::Done, TaskStatus::InProgress],
            'done -> cancelled' => [TaskStatus::Done, TaskStatus::Cancelled],
            'done -> blocked' => [TaskStatus::Done, TaskStatus::Blocked],

            'cancelled -> todo' => [TaskStatus::Cancelled, TaskStatus::Todo],
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

            'blocked -> todo' => [TaskStatus::Blocked, TaskStatus::Todo],
            'blocked -> done' => [TaskStatus::Blocked, TaskStatus::Done],
        ];
    }
}
