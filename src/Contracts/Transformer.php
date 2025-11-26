<?php

namespace SocialDept\AtpSchema\Contracts;

interface Transformer
{
    /**
     * Transform raw data to model.
     */
    public function fromArray(array $data): mixed;

    /**
     * Transform model to raw data.
     */
    public function toArray(mixed $model): array;

    /**
     * Check if this transformer supports the given type.
     */
    public function supports(string $type): bool;
}
