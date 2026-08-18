<?php

declare(strict_types=1);

namespace App\Domain\Task\Exceptions;

use App\Domain\Task\Enums\TaskStatus;
use DomainException;

/**
 * Domain error - violation of a business rule
 */
final class InvalidTaskStatusTransitionException extends DomainException
{
    public static function fromTo(TaskStatus $from, TaskStatus $to): self
    {
        return new self(sprintf(
            'Niedozwolone przejście statusu: z "%s" do "%s".',
            $from->value,
            $to->value
        ));
    }
}
