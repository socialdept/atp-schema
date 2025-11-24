<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Sync\ListHosts;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Com\Atproto\Sync\HostStatus;

/**
 * Lexicon: com.atproto.sync.listHosts.host
 * Type: object
 *
 * @property string $hostname hostname of server; not a URL (no scheme)
 * @property int|null $seq Recent repo stream event sequence number. May be delayed from actual stream processing (eg, persisted cursor not in-memory cursor).
 * @property int|null $accountCount
 * @property HostStatus|null $status
 *
 * Constraints:
 * - Required: hostname
 */
class Host extends Data
{

    /**
     * @param  string  $hostname  hostname of server; not a URL (no scheme)
     * @param  int|null  $seq  Recent repo stream event sequence number. May be delayed from actual stream processing (eg, persisted cursor not in-memory cursor).
     */
    public function __construct(
        public readonly string $hostname,
        public readonly ?int $seq = null,
        public readonly ?int $accountCount = null,
        public readonly ?HostStatus $status = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.sync.listHosts.host';
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
            hostname: $data['hostname'],
            seq: $data['seq'] ?? null,
            accountCount: $data['accountCount'] ?? null,
            status: isset($data['status']) ? Defs::fromArray($data['status']) : null
        );
    }

}
