<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: tools.ozone.moderation.defs.repoViewNotFound
 * Type: object
 *
 * @property string $did
 *
 * Constraints:
 * - Required: did
 * - did: Format: did
 */
class RepoViewNotFound extends Data
{

    /**
     */
    public function __construct(
        public readonly string $did
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.repoViewNotFound';
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
            did: $data['did']
        );
    }

}
