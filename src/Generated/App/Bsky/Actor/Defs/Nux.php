<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * A new user experiences (NUX) storage object
 *
 * Lexicon: app.bsky.actor.defs.nux
 * Type: object
 *
 * @property string $id
 * @property bool $completed
 * @property string|null $data Arbitrary data for the NUX. The structure is defined by the NUX itself. Limited to 300 characters.
 * @property Carbon|null $expiresAt The date and time at which the NUX will expire and should be considered completed.
 *
 * Constraints:
 * - Required: id, completed
 * - id: Max length: 100
 * - data: Max length: 3000
 * - data: Max graphemes: 300
 * - expiresAt: Format: datetime
 */
class Nux extends Data
{

    /**
     * @param  string|null  $data  Arbitrary data for the NUX. The structure is defined by the NUX itself. Limited to 300 characters.
     * @param  Carbon|null  $expiresAt  The date and time at which the NUX will expire and should be considered completed.
     */
    public function __construct(
        public readonly string $id,
        public readonly bool $completed,
        public readonly ?string $data = null,
        public readonly ?Carbon $expiresAt = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.nux';
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
            completed: $data['completed'],
            data: $data['data'] ?? null,
            expiresAt: isset($data['expiresAt']) ? Carbon::parse($data['expiresAt']) : null
        );
    }

}
