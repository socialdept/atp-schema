<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Server\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.server.defs.inviteCodeUse
 * Type: object
 *
 * @property string $usedBy
 * @property Carbon $usedAt
 *
 * Constraints:
 * - Required: usedBy, usedAt
 * - usedBy: Format: did
 * - usedAt: Format: datetime
 */
class InviteCodeUse extends Data
{
    public function __construct(
        public readonly string $usedBy,
        public readonly Carbon $usedAt
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.server.defs.inviteCodeUse';
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
            usedBy: $data['usedBy'],
            usedAt: Carbon::parse($data['usedAt'])
        );
    }

}
