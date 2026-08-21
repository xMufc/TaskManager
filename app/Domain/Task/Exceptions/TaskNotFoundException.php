<?php

namespace App\Domain\Task\Exceptions;

use DomainException;

/**
 * Domain error - violation of a business rule
 */
final class TaskNotFoundException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self("Zadanie o ID \"{$id}\" nie zostało znalezione.");
    }
}