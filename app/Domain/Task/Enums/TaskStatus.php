<?php

namespace App\Domain\Task\Enums;

use App\Domain\Task\Exceptions\InvalidTaskStatusTransitionException;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Blocked = 'blocked';
    case Cancelled = 'cancelled';

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Todo => [self::InProgress, self::Cancelled],
            self::InProgress => [self::Done, self::Cancelled, self::Blocked],
            self::Done => [],
            self::Blocked => [self::InProgress, self::Cancelled],
            self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return false;
        }

        return in_array($target, $this->allowedTransitions(), true);
    }

    public static function assertAllowed($from, $target): void
    {
        if (! $from->canTransitionTo($target)) {
            throw InvalidTaskStatusTransitionException::fromTo($from, $target);
        }
    }

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'Do zrobienia',
            self::InProgress => 'W trakcie',
            self::Done => 'Zakończone',
            self::Blocked => 'Zablokowane',
            self::Cancelled => 'Anulowane',
        };
    }
}
