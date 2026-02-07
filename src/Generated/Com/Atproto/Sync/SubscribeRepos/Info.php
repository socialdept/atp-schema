<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Sync\SubscribeRepos;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.sync.subscribeRepos.info
 * Type: object
 *
 * @property string $name
 * @property string|null $message
 *
 * Constraints:
 * - Required: name
 */
#[Generated(regenerate: true)]
class Info extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $message = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.sync.subscribeRepos.info';
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
