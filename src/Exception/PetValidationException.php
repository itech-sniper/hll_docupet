<?php

namespace App\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Exception thrown when pet validation fails.
 */
class PetValidationException extends \Exception
{
    private ConstraintViolationListInterface $violations;

    public function __construct(
        ConstraintViolationListInterface $violations,
        string $message = 'Pet validation failed',
        int $code = 0,
        ?\Exception $previous = null,
    ) {
        $this->violations = $violations;
        parent::__construct($message, $code, $previous);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    public function getViolationMessages(): array
    {
        $messages = [];
        foreach ($this->violations as $violation) {
            $messages[] = $violation->getMessage();
        }

        return $messages;
    }

    public static function fromViolations(ConstraintViolationListInterface $violations): self
    {
        return new self($violations);
    }
}
