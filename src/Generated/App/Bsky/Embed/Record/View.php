<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\Record;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.record.view
 * Type: object
 *
 * @property mixed $record
 *
 * Constraints:
 * - Required: record
 */
#[Generated(regenerate: true)]
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
