<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Graph\Defs\StarterPackViewBasic;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\Label;
use SocialDept\AtpSchema\Generated\Com\Atproto\Repo\StrongRef;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.profileViewDetailed
 * Type: object
 *
 * @property string $did
 * @property string $handle
 * @property string|null $displayName
 * @property string|null $description
 * @property string|null $pronouns
 * @property string|null $website
 * @property string|null $avatar
 * @property string|null $banner
 * @property int|null $followersCount
 * @property int|null $followsCount
 * @property int|null $postsCount
 * @property mixed $associated
 * @property StarterPackViewBasic|null $joinedViaStarterPack
 * @property Carbon|null $indexedAt
 * @property Carbon|null $createdAt
 * @property mixed $viewer
 * @property array<Label>|null $labels
 * @property StrongRef|null $pinnedPost
 * @property mixed $verification
 * @property mixed $status
 * @property mixed $debug Debug information for internal development
 *
 * Constraints:
 * - Required: did, handle
 * - did: Format: did
 * - handle: Format: handle
 * - displayName: Max length: 640
 * - displayName: Max graphemes: 64
 * - description: Max length: 2560
 * - description: Max graphemes: 256
 * - website: Format: uri
 * - avatar: Format: uri
 * - banner: Format: uri
 * - indexedAt: Format: datetime
 * - createdAt: Format: datetime
 */
#[Generated(regenerate: true)]
class ProfileViewDetailed extends Data
{
    /**
     * @param  mixed  $debug  Debug information for internal development
     */
    public function __construct(
        public readonly string $did,
        public readonly string $handle,
        public readonly ?string $displayName = null,
        public readonly ?string $description = null,
        public readonly ?string $pronouns = null,
        public readonly ?string $website = null,
        public readonly ?string $avatar = null,
        public readonly ?string $banner = null,
        public readonly ?int $followersCount = null,
        public readonly ?int $followsCount = null,
        public readonly ?int $postsCount = null,
        public readonly mixed $associated = null,
        public readonly ?StarterPackViewBasic $joinedViaStarterPack = null,
        public readonly ?Carbon $indexedAt = null,
        public readonly ?Carbon $createdAt = null,
        public readonly mixed $viewer = null,
        public readonly ?array $labels = null,
        public readonly ?StrongRef $pinnedPost = null,
        public readonly mixed $verification = null,
        public readonly mixed $status = null,
        public readonly mixed $debug = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.profileViewDetailed';
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
            did: $data['did'],
            handle: $data['handle'],
            displayName: $data['displayName'] ?? null,
            description: $data['description'] ?? null,
            pronouns: $data['pronouns'] ?? null,
            website: $data['website'] ?? null,
            avatar: $data['avatar'] ?? null,
            banner: $data['banner'] ?? null,
            followersCount: $data['followersCount'] ?? null,
            followsCount: $data['followsCount'] ?? null,
            postsCount: $data['postsCount'] ?? null,
            associated: isset($data['associated']) ? ProfileAssociated::fromArray($data['associated']) : null,
            joinedViaStarterPack: isset($data['joinedViaStarterPack']) ? StarterPackViewBasic::fromArray($data['joinedViaStarterPack']) : null,
            indexedAt: isset($data['indexedAt']) ? Carbon::parse($data['indexedAt']) : null,
            createdAt: isset($data['createdAt']) ? Carbon::parse($data['createdAt']) : null,
            viewer: isset($data['viewer']) ? ViewerState::fromArray($data['viewer']) : null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : [],
            pinnedPost: isset($data['pinnedPost']) ? StrongRef::fromArray($data['pinnedPost']) : null,
            verification: isset($data['verification']) ? VerificationState::fromArray($data['verification']) : null,
            status: isset($data['status']) ? StatusView::fromArray($data['status']) : null,
            debug: $data['debug'] ?? null
        );
    }

}
