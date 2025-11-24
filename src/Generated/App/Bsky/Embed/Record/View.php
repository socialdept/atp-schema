<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed\Record;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: app.bsky.embed.record.view
 * Type: object
 *
 * @property mixed $record
 *
 * Constraints:
 * - Required: record
 */
class View extends Data
{

    public function __construct(
        public readonly mixed $record
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.record.view';
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
            record: UnionHelper::validateOpenUnion($data['record'])
        );
    }

}
