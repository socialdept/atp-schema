<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\ProfileViewBasic;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: app.bsky.feed.defs.replyRef
 * Type: object
 *
 * @property mixed $root
 * @property mixed $parent
 * @property ProfileViewBasic|null $grandparentAuthor When parent is a reply to another post, this is the author of that post.
 *
 * Constraints:
 * - Required: root, parent
 */
class ReplyRef extends Data
{
    /**
     * @param  ProfileViewBasic|null  $grandparentAuthor  When parent is a reply to another post, this is the author of that post.
     */
    public function __construct(
        public readonly mixed $root,
        public readonly mixed $parent,
        public readonly ?ProfileViewBasic $grandparentAuthor = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.replyRef';
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
            root: UnionHelper::validateOpenUnion($data['root']),
            parent: UnionHelper::validateOpenUnion($data['parent']),
            grandparentAuthor: isset($data['grandparentAuthor']) ? Defs::fromArray($data['grandparentAuthor']) : null
        );
    }

}
