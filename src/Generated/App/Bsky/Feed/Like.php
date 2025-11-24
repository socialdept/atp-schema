<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Com\Atproto\Repo\StrongRef;

/**
 * Lexicon: app.bsky.feed.like
 * Type: record
 */
class Like extends Data
{

    public function __construct(
        public readonly StrongRef $subject,
        public readonly Carbon $createdAt,
        public readonly ?StrongRef $via = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.like';
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
            subject: StrongRef::fromArray($data['subject']),
            createdAt: Carbon::parse($data['createdAt']),
            via: isset($data['via']) ? StrongRef::fromArray($data['via']) : null
        );
    }

}
