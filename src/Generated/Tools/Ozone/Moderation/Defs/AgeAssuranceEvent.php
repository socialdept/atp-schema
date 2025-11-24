<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Age assurance info coming directly from users. Only works on DID subjects.
 *
 * Lexicon: tools.ozone.moderation.defs.ageAssuranceEvent
 * Type: object
 *
 * @property Carbon $createdAt The date and time of this write operation.
 * @property string $status The status of the age assurance process.
 * @property string $attemptId The unique identifier for this instance of the age assurance flow, in UUID format.
 * @property string|null $initIp The IP address used when initiating the AA flow.
 * @property string|null $initUa The user agent used when initiating the AA flow.
 * @property string|null $completeIp The IP address used when completing the AA flow.
 * @property string|null $completeUa The user agent used when completing the AA flow.
 *
 * Constraints:
 * - Required: createdAt, status, attemptId
 * - createdAt: Format: datetime
 */
class AgeAssuranceEvent extends Data
{

    /**
     * @param  Carbon  $createdAt  The date and time of this write operation.
     * @param  string  $status  The status of the age assurance process.
     * @param  string  $attemptId  The unique identifier for this instance of the age assurance flow, in UUID format.
     * @param  string|null  $initIp  The IP address used when initiating the AA flow.
     * @param  string|null  $initUa  The user agent used when initiating the AA flow.
     * @param  string|null  $completeIp  The IP address used when completing the AA flow.
     * @param  string|null  $completeUa  The user agent used when completing the AA flow.
     */
    public function __construct(
        public readonly Carbon $createdAt,
        public readonly string $status,
        public readonly string $attemptId,
        public readonly ?string $initIp = null,
        public readonly ?string $initUa = null,
        public readonly ?string $completeIp = null,
        public readonly ?string $completeUa = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.ageAssuranceEvent';
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
            createdAt: Carbon::parse($data['createdAt']),
            status: $data['status'],
            attemptId: $data['attemptId'],
            initIp: $data['initIp'] ?? null,
            initUa: $data['initUa'] ?? null,
            completeIp: $data['completeIp'] ?? null,
            completeUa: $data['completeUa'] ?? null
        );
    }

}
