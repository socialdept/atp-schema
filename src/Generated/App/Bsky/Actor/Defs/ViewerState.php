<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Graph\Defs\ListViewBasic;
use SocialDept\AtpSchema\Generated\App\Bsky\Notification\Defs\ActivitySubscription;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Metadata about the requesting account's relationship with the subject
 * account. Only has meaningful content for authed requests.
 *
 * Lexicon: app.bsky.actor.defs.viewerState
 * Type: object
 *
 * @property bool|null $muted
 * @property ListViewBasic|null $mutedByList
 * @property bool|null $blockedBy
 * @property string|null $blocking
 * @property ListViewBasic|null $blockingByList
 * @property string|null $following
 * @property string|null $followedBy
 * @property mixed $knownFollowers This property is present only in selected cases, as an optimization.
 * @property ActivitySubscription|null $activitySubscription This property is present only in selected cases, as an optimization.
 *
 * Constraints:
 * - blocking: Format: at-uri
 * - following: Format: at-uri
 * - followedBy: Format: at-uri
 */
#[Generated(regenerate: true)]
class ViewerState extends Data
{
    /**
     * @param  mixed  $knownFollowers  This property is present only in selected cases, as an optimization.
     * @param  ActivitySubscription|null  $activitySubscription  This property is present only in selected cases, as an optimization.
     */
    public function __construct(
        public readonly ?bool $muted = null,
        public readonly ?ListViewBasic $mutedByList = null,
        public readonly ?bool $blockedBy = null,
        public readonly ?string $blocking = null,
        public readonly ?ListViewBasic $blockingByList = null,
        public readonly ?string $following = null,
        public readonly ?string $followedBy = null,
        public readonly mixed $knownFollowers = null,
        public readonly ?ActivitySubscription $activitySubscription = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.viewerState';
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
            muted: $data['muted'] ?? null,
            mutedByList: isset($data['mutedByList']) ? ListViewBasic::fromArray($data['mutedByList']) : null,
            blockedBy: $data['blockedBy'] ?? null,
            blocking: $data['blocking'] ?? null,
            blockingByList: isset($data['blockingByList']) ? ListViewBasic::fromArray($data['blockingByList']) : null,
            following: $data['following'] ?? null,
            followedBy: $data['followedBy'] ?? null,
            knownFollowers: isset($data['knownFollowers']) ? KnownFollowers::fromArray($data['knownFollowers']) : null,
            activitySubscription: isset($data['activitySubscription']) ? ActivitySubscription::fromArray($data['activitySubscription']) : null
        );
    }

}
