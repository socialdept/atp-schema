<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.actor.defs.personalDetailsPref
 * Type: object
 *
 * @property Carbon|null $birthDate The birth date of account owner.
 *
 * Constraints:
 * - birthDate: Format: datetime
 */
class PersonalDetailsPref extends Data
{

    /**
     * @param  Carbon|null  $birthDate  The birth date of account owner.
     */
    public function __construct(
        public readonly ?Carbon $birthDate = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.personalDetailsPref';
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
            birthDate: isset($data['birthDate']) ? Carbon::parse($data['birthDate']) : null
        );
    }

}
