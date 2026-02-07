<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\RecordWithMedia;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Embed\Record\View;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.recordWithMedia.view
 * Type: object
 *
 * @property View $record
 * @property mixed $media
 *
 * Constraints:
 * - Required: record, media
 */
#[Generated(regenerate: true)]
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
            record: View::fromArray($data['record']),
            media: UnionHelper::validateOpenUnion($data['media'])
        );
    }

}
