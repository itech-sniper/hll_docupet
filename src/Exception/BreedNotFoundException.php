<?php

namespace App\Exception;

use Exception;

/**
 * Exception thrown when a requested breed is not found.
 */
class BreedNotFoundException extends \Exception
{
    public function __construct(int $breedId, ?string $message = null, int $code = 0, ?\Exception $previous = null)
    {
        $message = $message ?: sprintf('Breed with ID %d not found', $breedId);
        parent::__construct($message, $code, $previous);
    }

    public static function forId(int $breedId): self
    {
        return new self($breedId);
    }

    public static function forName(string $breedName): self
    {
        return new self(0, sprintf('Breed with name "%s" not found', $breedName));
    }
}
