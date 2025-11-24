<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Label\SubscribeLabels;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.label.subscribeLabels.info
 * Type: object
 *
 * @property string $name
 * @property string|null $message
 *
 * Constraints:
 * - Required: name
 */
class Info extends Data
{

    /**
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $message = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.label.subscribeLabels.info';
    }


    /**
     * Create an instance from an array.
     *
     * @param  array  $data  The data array
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name'],
            message: $data['message'] ?? null
        );
    }

}
