<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ProfileViewBasic;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
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
#[Generated(regenerate: true)]
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
            grandparentAuthor: isset($data['grandparentAuthor']) ? ProfileViewBasic::fromArray($data['grandparentAuthor']) : null
        );
    }

}
