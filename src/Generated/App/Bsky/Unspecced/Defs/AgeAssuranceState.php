<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * The computed state of the age assurance process, returned to the user in
 * question on certain authenticated requests.
 *
 * Lexicon: app.bsky.unspecced.defs.ageAssuranceState
 * Type: object
 *
 * @property Carbon|null $lastInitiatedAt The timestamp when this state was last updated.
 * @property string $status The status of the age assurance process.
 *
 * Constraints:
 * - Required: status
 * - lastInitiatedAt: Format: datetime
 */
#[Generated(regenerate: true)]
class AgeAssuranceState extends Data
{
    /**
     * @param  string  $status  The status of the age assurance process.
     * @param  Carbon|null  $lastInitiatedAt  The timestamp when this state was last updated.
     */
    public function __construct(
        public readonly string $status,
        public readonly ?Carbon $lastInitiatedAt = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.unspecced.defs.ageAssuranceState';
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
            status: $data['status'],
            lastInitiatedAt: isset($data['lastInitiatedAt']) ? Carbon::parse($data['lastInitiatedAt']) : null
        );
    }

}
