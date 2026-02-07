<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.defs.threadViewPost
 * Type: object
 *
 * @property mixed $post
 * @property mixed $parent
 * @property array|null $replies
 * @property mixed $threadContext
 *
 * Constraints:
 * - Required: post
 */
#[Generated(regenerate: true)]
class ThreadViewPost extends Data
{
    public function __construct(
        public readonly mixed $post,
        public readonly mixed $parent = null,
        public readonly ?array $replies = null,
        public readonly mixed $threadContext = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.threadViewPost';
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
            post: PostView::fromArray($data['post']),
            parent: isset($data['parent']) ? UnionHelper::validateOpenUnion($data['parent']) : null,
            replies: $data['replies'] ?? null,
            threadContext: isset($data['threadContext']) ? ThreadContext::fromArray($data['threadContext']) : null
        );
    }

}
