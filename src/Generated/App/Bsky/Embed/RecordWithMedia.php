<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * A representation of a record embedded in a Bluesky record (eg, a post),
 * alongside other compatible embeds. For example, a quote post and image, or a
 * quote post and external URL card.
 *
 * Lexicon: app.bsky.embed.recordWithMedia
 * Type: object
 *
 * @property Record $record
 * @property mixed $media
 *
 * Constraints:
 * - Required: record, media
 */
#[Generated(regenerate: true)]
class RecordWithMedia extends Data
{
    public function __construct(
        public readonly Record $record,
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
        return 'app.bsky.embed.recordWithMedia';
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
