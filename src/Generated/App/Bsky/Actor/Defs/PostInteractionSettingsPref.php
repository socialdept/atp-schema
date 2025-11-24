<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Default post interaction settings for the account. These values should be
 * applied as default values when creating new posts. These refs should mirror
 * the threadgate and postgate records exactly.
 *
 * Lexicon: app.bsky.actor.defs.postInteractionSettingsPref
 * Type: object
 *
 * @property array|null $threadgateAllowRules Matches threadgate record. List of rules defining who can reply to this users posts. If value is an empty array, no one can reply. If value is undefined, anyone can reply.
 * @property array|null $postgateEmbeddingRules Matches postgate record. List of rules defining who can embed this users posts. If value is an empty array or is undefined, no particular rules apply and anyone can embed.
 *
 * Constraints:
 * - threadgateAllowRules: Max length: 5
 * - postgateEmbeddingRules: Max length: 5
 */
class PostInteractionSettingsPref extends Data
{
    /**
     * @param  array|null  $threadgateAllowRules  Matches threadgate record. List of rules defining who can reply to this users posts. If value is an empty array, no one can reply. If value is undefined, anyone can reply.
     * @param  array|null  $postgateEmbeddingRules  Matches postgate record. List of rules defining who can embed this users posts. If value is an empty array or is undefined, no particular rules apply and anyone can embed.
     */
    public function __construct(
        public readonly ?array $threadgateAllowRules = null,
        public readonly ?array $postgateEmbeddingRules = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.postInteractionSettingsPref';
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
            threadgateAllowRules: $data['threadgateAllowRules'] ?? null,
            postgateEmbeddingRules: $data['postgateEmbeddingRules'] ?? null
        );
    }

}
