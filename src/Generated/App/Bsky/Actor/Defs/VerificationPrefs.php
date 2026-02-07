<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Preferences for how verified accounts appear in the app.
 *
 * Lexicon: app.bsky.actor.defs.verificationPrefs
 * Type: object
 *
 * @property bool|null $hideBadges Hide the blue check badges for verified accounts and trusted verifiers.
 */
#[Generated(regenerate: true)]
class VerificationPrefs extends Data
{
    /**
     * @param  bool|null  $hideBadges  Hide the blue check badges for verified accounts and trusted verifiers.
     */
    public function __construct(
        public readonly ?bool $hideBadges = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.verificationPrefs';
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
            hideBadges: $data['hideBadges'] ?? null
        );
    }

}
