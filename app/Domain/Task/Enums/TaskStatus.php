<?php

namespace App\Domain\Task\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'Do zrobienia',
            self::InProgress => 'W trakcie',
            self::Done => 'Zakończone',
            self::Cancelled => 'Anulowane',
        };
    }
}