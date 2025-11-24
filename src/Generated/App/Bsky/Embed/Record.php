<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Com\Atproto\Repo\StrongRef;

/**
 * A representation of a record embedded in a Bluesky record (eg, a post). For
 * example, a quote-post, or sharing a feed generator record.
 *
 * Lexicon: app.bsky.embed.record
 * Type: object
 *
 * @property StrongRef $record
 *
 * Constraints:
 * - Required: record
 */
class Record extends Data
{
    /**
     */
    public function __construct(
        public readonly StrongRef $record
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.record';
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
            record: StrongRef::fromArray($data['record'])
        );
    }

}
