<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed\RecordWithMedia;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Embed\View;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: app.bsky.embed.recordWithMedia.view
 * Type: object
 *
 * @property View $record
 * @property mixed $media
 *
 * Constraints:
 * - Required: record, media
 */
class View extends Data
{

    public function __construct(
        public readonly View $record,
        public readonly mixed $media
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.recordWithMedia.view';
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
            record: Record::fromArray($data['record']),
            media: UnionHelper::validateOpenUnion($data['media'])
        );
    }

}
