<?php

namespace App\Exception;

use Exception;

/**
 * Exception thrown when a requested pet type is not found
 */
class PetTypeNotFoundException extends Exception
{
    public function __construct(int $petTypeId, string $message = null, int $code = 0, Exception $previous = null)
    {
        $message = $message ?: sprintf('Pet type with ID %d not found', $petTypeId);
        parent::__construct($message, $code, $previous);
    }

    public static function forId(int $petTypeId): self
    {
        return new self($petTypeId);
    }

    public static function forName(string $typeName): self
    {
        return new self(0, sprintf('Pet type with name "%s" not found', $typeName));
    }
}
