<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * A grab bag of state that's specific to the bsky.app program. Third-party apps
 * shouldn't use this.
 *
 * Lexicon: app.bsky.actor.defs.bskyAppStatePref
 * Type: object
 *
 * @property mixed $activeProgressGuide
 * @property array<string>|null $queuedNudges An array of tokens which identify nudges (modals, popups, tours, highlight dots) that should be shown to the user.
 * @property array<Nux>|null $nuxs Storage for NUXs the user has encountered.
 *
 * Constraints:
 * - queuedNudges: Max length: 1000
 * - nuxs: Max length: 100
 */
class BskyAppStatePref extends Data
{
    /**
     * @param  array<string>|null  $queuedNudges  An array of tokens which identify nudges (modals, popups, tours, highlight dots) that should be shown to the user.
     * @param  array<Nux>|null  $nuxs  Storage for NUXs the user has encountered.
     */
    public function __construct(
        public readonly mixed $activeProgressGuide = null,
        public readonly ?array $queuedNudges = null,
        public readonly ?array $nuxs = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.bskyAppStatePref';
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
            activeProgressGuide: $data['activeProgressGuide'] ?? null,
            queuedNudges: $data['queuedNudges'] ?? null,
            nuxs: isset($data['nuxs']) ? array_map(fn ($item) => Nux::fromArray($item), $data['nuxs']) : []
        );
    }

}
