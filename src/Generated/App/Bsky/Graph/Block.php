<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.graph.block
 * Type: record
 */
class Block extends Data
{

    /**
     * @param  string  $subject  DID of the account to be blocked.
     */
    public function __construct(
        public readonly string $subject,
        public readonly Carbon $createdAt
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.block';
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
            subject: $data['subject'],
            createdAt: Carbon::parse($data['createdAt'])
        );
    }

}
