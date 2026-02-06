<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs\PostView;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.unspecced.defs.threadItemPost
 * Type: object
 *
 * @property PostView $post
 * @property bool $moreParents This post has more parents that were not present in the response. This is just a boolean, without the number of parents.
 * @property int $moreReplies This post has more replies that were not present in the response. This is a numeric value, which is best-effort and might not be accurate.
 * @property bool $opThread This post is part of a contiguous thread by the OP from the thread root. Many different OP threads can happen in the same thread.
 * @property bool $hiddenByThreadgate The threadgate created by the author indicates this post as a reply to be hidden for everyone consuming the thread.
 * @property bool $mutedByViewer This is by an account muted by the viewer requesting it.
 *
 * Constraints:
 * - Required: post, moreParents, moreReplies, opThread, hiddenByThreadgate, mutedByViewer
 */
#[Generated(regenerate: true)]
class ThreadItemPost extends Data
{
    /**
     * @param  bool  $moreParents  This post has more parents that were not present in the response. This is just a boolean, without the number of parents.
     * @param  int  $moreReplies  This post has more replies that were not present in the response. This is a numeric value, which is best-effort and might not be accurate.
     * @param  bool  $opThread  This post is part of a contiguous thread by the OP from the thread root. Many different OP threads can happen in the same thread.
     * @param  bool  $hiddenByThreadgate  The threadgate created by the author indicates this post as a reply to be hidden for everyone consuming the thread.
     * @param  bool  $mutedByViewer  This is by an account muted by the viewer requesting it.
     */
    public function __construct(
        public readonly PostView $post,
        public readonly bool $moreParents,
        public readonly int $moreReplies,
        public readonly bool $opThread,
        public readonly bool $hiddenByThreadgate,
        public readonly bool $mutedByViewer
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.unspecced.defs.threadItemPost';
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
            moreParents: $data['moreParents'],
            moreReplies: $data['moreReplies'],
            opThread: $data['opThread'],
            hiddenByThreadgate: $data['hiddenByThreadgate'],
            mutedByViewer: $data['mutedByViewer']
        );
    }

}
