<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.actor.defs.profileAssociated
 * Type: object
 *
 * @property int|null $lists
 * @property int|null $feedgens
 * @property int|null $starterPacks
 * @property bool|null $labeler
 * @property mixed $chat
 * @property mixed $activitySubscription
 */
class ProfileAssociated extends Data
{
    /**
     */
    public function __construct(
        public readonly ?int $lists = null,
        public readonly ?int $feedgens = null,
        public readonly ?int $starterPacks = null,
        public readonly ?bool $labeler = null,
        public readonly mixed $chat = null,
        public readonly mixed $activitySubscription = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.profileAssociated';
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
            lists: $data['lists'] ?? null,
            feedgens: $data['feedgens'] ?? null,
            starterPacks: $data['starterPacks'] ?? null,
            labeler: $data['labeler'] ?? null,
            chat: $data['chat'] ?? null,
            activitySubscription: $data['activitySubscription'] ?? null
        );
    }

}
