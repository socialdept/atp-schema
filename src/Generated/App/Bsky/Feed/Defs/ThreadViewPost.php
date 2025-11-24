<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Feed\Defs\ThreadViewPost\PostView;
use SocialDept\Schema\Generated\App\Bsky\Feed\Defs\ThreadViewPost\ThreadContext;
use SocialDept\Schema\Support\UnionHelper;

/**
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
class ThreadViewPost extends Data
{

    /**
     */
    public function __construct(
        public readonly mixed $post,
        public readonly mixed $parent = null,
        public readonly ?array $replies = null,
        public readonly mixed $threadContext = null
    ) {}

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
            post: $data['post'],
            parent: isset($data['parent']) ? UnionHelper::validateOpenUnion($data['parent']) : null,
            replies: $data['replies'] ?? null,
            threadContext: $data['threadContext'] ?? null
        );
    }

}
