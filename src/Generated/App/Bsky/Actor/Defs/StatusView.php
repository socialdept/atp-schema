<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.statusView
 * Type: object
 *
 * @property string $status The status for the account.
 * @property mixed $record
 * @property mixed $embed An optional embed associated with the status.
 * @property Carbon|null $expiresAt The date when this status will expire. The application might choose to no longer return the status after expiration.
 * @property bool|null $isActive True if the status is not expired, false if it is expired. Only present if expiration was set.
 *
 * Constraints:
 * - Required: status, record
 * - expiresAt: Format: datetime
 */
class StatusView extends Data
{
    /**
     * @param  string  $status  The status for the account.
     * @param  mixed  $embed  An optional embed associated with the status.
     * @param  Carbon|null  $expiresAt  The date when this status will expire. The application might choose to no longer return the status after expiration.
     * @param  bool|null  $isActive  True if the status is not expired, false if it is expired. Only present if expiration was set.
     */
    public function __construct(
        public readonly string $status,
        public readonly mixed $record,
        public readonly mixed $embed = null,
        public readonly ?Carbon $expiresAt = null,
        public readonly ?bool $isActive = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.statusView';
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
            record: $data['record'],
            embed: isset($data['embed']) ? UnionHelper::validateOpenUnion($data['embed']) : null,
            expiresAt: isset($data['expiresAt']) ? Carbon::parse($data['expiresAt']) : null,
            isActive: $data['isActive'] ?? null
        );
    }

}
