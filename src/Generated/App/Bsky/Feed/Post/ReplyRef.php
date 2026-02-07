<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Post;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\Com\Atproto\Repo\StrongRef;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.post.replyRef
 * Type: object
 *
 * @property StrongRef $root
 * @property StrongRef $parent
 *
 * Constraints:
 * - Required: root, parent
 */
#[Generated(regenerate: true)]
class ReplyRef extends Data
{
    public function __construct(
        public readonly StrongRef $root,
        public readonly StrongRef $parent
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.post.replyRef';
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
            root: StrongRef::fromArray($data['root']),
            parent: StrongRef::fromArray($data['parent'])
        );
    }

}
