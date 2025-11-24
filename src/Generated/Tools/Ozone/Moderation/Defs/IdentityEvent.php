<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Logs identity related events on a repo subject. Normally captured by automod
 * from the firehose and emitted to ozone for historical tracking.
 *
 * Lexicon: tools.ozone.moderation.defs.identityEvent
 * Type: object
 *
 * @property string|null $comment
 * @property string|null $handle
 * @property string|null $pdsHost
 * @property bool|null $tombstone
 * @property Carbon $timestamp
 *
 * Constraints:
 * - Required: timestamp
 * - handle: Format: handle
 * - pdsHost: Format: uri
 * - timestamp: Format: datetime
 */
class IdentityEvent extends Data
{
    public function __construct(
        public readonly Carbon $timestamp,
        public readonly ?string $comment = null,
        public readonly ?string $handle = null,
        public readonly ?string $pdsHost = null,
        public readonly ?bool $tombstone = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.identityEvent';
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
            timestamp: Carbon::parse($data['timestamp']),
            comment: $data['comment'] ?? null,
            handle: $data['handle'] ?? null,
            pdsHost: $data['pdsHost'] ?? null,
            tombstone: $data['tombstone'] ?? null
        );
    }

}
