<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Logs lifecycle event on a record subject. Normally captured by automod from
 * the firehose and emitted to ozone for historical tracking.
 *
 * Lexicon: tools.ozone.moderation.defs.recordEvent
 * Type: object
 *
 * @property string|null $comment
 * @property string $op
 * @property string|null $cid
 * @property Carbon $timestamp
 *
 * Constraints:
 * - Required: timestamp, op
 * - cid: Format: cid
 * - timestamp: Format: datetime
 */
class RecordEvent extends Data
{

    /**
     */
    public function __construct(
        public readonly string $op,
        public readonly Carbon $timestamp,
        public readonly ?string $comment = null,
        public readonly ?string $cid = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.recordEvent';
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
            op: $data['op'],
            timestamp: Carbon::parse($data['timestamp']),
            comment: $data['comment'] ?? null,
            cid: $data['cid'] ?? null
        );
    }

}
