<?php

namespace SocialDept\Schema\Exceptions;

use Exception;

class SchemaException extends Exception
{
    /**
     * Additional context data for the exception.
     */
    protected array $context = [];

    /**
     * Set context data.
     */
    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context data.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Create exception with context.
     */
    public static function withContext(string $message, array $context = []): self
    {
        return (new static($message))->setContext($context);
    }
}
