<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Post;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Deprecated: use facets instead.
 *
 * Lexicon: app.bsky.feed.post.entity
 * Type: object
 *
 * @property mixed $index
 * @property string $type Expected values are 'mention' and 'link'.
 * @property string $value
 *
 * Constraints:
 * - Required: index, type, value
 */
class Entity extends Data
{
    /**
     * @param  string  $type  Expected values are 'mention' and 'link'.
     */
    public function __construct(
        public readonly mixed $index,
        public readonly string $type,
        public readonly string $value
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.post.entity';
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
            index: $data['index'],
            type: $data['type'],
            value: $data['value']
        );
    }

}
