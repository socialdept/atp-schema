<?php

namespace SocialDept\Schema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\Defs\ProfileViewBasic;
use SocialDept\Schema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.convoView
 * Type: object
 *
 * @property string $id
 * @property string $rev
 * @property array<ProfileViewBasic> $members
 * @property mixed $lastMessage
 * @property mixed $lastReaction
 * @property bool $muted
 * @property string|null $status
 * @property int $unreadCount
 *
 * Constraints:
 * - Required: id, rev, members, muted, unreadCount
 */
class ConvoView extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $rev,
        public readonly array $members,
        public readonly bool $muted,
        public readonly int $unreadCount,
        public readonly mixed $lastMessage = null,
        public readonly mixed $lastReaction = null,
        public readonly ?string $status = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.convoView';
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
            id: $data['id'],
            rev: $data['rev'],
            members: isset($data['members']) ? array_map(fn ($item) => ProfileViewBasic::fromArray($item), $data['members']) : [],
            muted: $data['muted'],
            unreadCount: $data['unreadCount'],
            lastMessage: isset($data['lastMessage']) ? UnionHelper::validateOpenUnion($data['lastMessage']) : null,
            lastReaction: isset($data['lastReaction']) ? UnionHelper::validateOpenUnion($data['lastReaction']) : null,
            status: $data['status'] ?? null
        );
    }

}
