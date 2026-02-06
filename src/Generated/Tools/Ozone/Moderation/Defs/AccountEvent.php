<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Logs account status related events on a repo subject. Normally captured by
 * automod from the firehose and emitted to ozone for historical tracking.
 *
 * Lexicon: tools.ozone.moderation.defs.accountEvent
 * Type: object
 *
 * @property string|null $comment
 * @property bool $active Indicates that the account has a repository which can be fetched from the host that emitted this event.
 * @property string|null $status
 * @property Carbon $timestamp
 *
 * Constraints:
 * - Required: timestamp, active
 * - timestamp: Format: datetime
 */
#[Generated(regenerate: true)]
class AccountEvent extends Data
{
    /**
     * @param  bool  $active  Indicates that the account has a repository which can be fetched from the host that emitted this event.
     */
    public function __construct(
        public readonly bool $active,
        public readonly Carbon $timestamp,
        public readonly ?string $comment = null,
        public readonly ?string $status = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.accountEvent';
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
            active: $data['active'],
            timestamp: Carbon::parse($data['timestamp']),
            comment: $data['comment'] ?? null,
            status: $data['status'] ?? null
        );
    }

}
