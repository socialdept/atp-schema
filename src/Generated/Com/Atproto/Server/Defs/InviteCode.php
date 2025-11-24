<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Server\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.server.defs.inviteCode
 * Type: object
 *
 * @property string $code
 * @property int $available
 * @property bool $disabled
 * @property string $forAccount
 * @property string $createdBy
 * @property Carbon $createdAt
 * @property array $uses
 *
 * Constraints:
 * - Required: code, available, disabled, forAccount, createdBy, createdAt, uses
 * - createdAt: Format: datetime
 */
class InviteCode extends Data
{
    /**
     */
    public function __construct(
        public readonly string $code,
        public readonly int $available,
        public readonly bool $disabled,
        public readonly string $forAccount,
        public readonly string $createdBy,
        public readonly Carbon $createdAt,
        public readonly array $uses
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.server.defs.inviteCode';
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
            code: $data['code'],
            available: $data['available'],
            disabled: $data['disabled'],
            forAccount: $data['forAccount'],
            createdBy: $data['createdBy'],
            createdAt: Carbon::parse($data['createdAt']),
            uses: $data['uses'] ?? []
        );
    }

}
