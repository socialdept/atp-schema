<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Take down a subject permanently or temporarily
 *
 * Lexicon: tools.ozone.moderation.defs.modEventTakedown
 * Type: object
 *
 * @property string|null $comment
 * @property int|null $durationInHours Indicates how long the takedown should be in effect before automatically expiring.
 * @property bool|null $acknowledgeAccountSubjects If true, all other reports on content authored by this account will be resolved (acknowledged).
 * @property array<string>|null $policies Names/Keywords of the policies that drove the decision.
 * @property string|null $severityLevel Severity level of the violation (e.g., 'sev-0', 'sev-1', 'sev-2', etc.).
 * @property array<string>|null $targetServices List of services where the takedown should be applied. If empty or not provided, takedown is applied on all configured services.
 * @property int|null $strikeCount Number of strikes to assign to the user for this violation.
 * @property Carbon|null $strikeExpiresAt When the strike should expire. If not provided, the strike never expires.
 *
 * Constraints:
 * - policies: Max length: 5
 * - strikeExpiresAt: Format: datetime
 */
class ModEventTakedown extends Data
{

    /**
     * @param  int|null  $durationInHours  Indicates how long the takedown should be in effect before automatically expiring.
     * @param  bool|null  $acknowledgeAccountSubjects  If true, all other reports on content authored by this account will be resolved (acknowledged).
     * @param  array<string>|null  $policies  Names/Keywords of the policies that drove the decision.
     * @param  string|null  $severityLevel  Severity level of the violation (e.g., 'sev-0', 'sev-1', 'sev-2', etc.).
     * @param  array<string>|null  $targetServices  List of services where the takedown should be applied. If empty or not provided, takedown is applied on all configured services.
     * @param  int|null  $strikeCount  Number of strikes to assign to the user for this violation.
     * @param  Carbon|null  $strikeExpiresAt  When the strike should expire. If not provided, the strike never expires.
     */
    public function __construct(
        public readonly ?string $comment = null,
        public readonly ?int $durationInHours = null,
        public readonly ?bool $acknowledgeAccountSubjects = null,
        public readonly ?array $policies = null,
        public readonly ?string $severityLevel = null,
        public readonly ?array $targetServices = null,
        public readonly ?int $strikeCount = null,
        public readonly ?Carbon $strikeExpiresAt = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventTakedown';
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
            comment: $data['comment'] ?? null,
            durationInHours: $data['durationInHours'] ?? null,
            acknowledgeAccountSubjects: $data['acknowledgeAccountSubjects'] ?? null,
            policies: $data['policies'] ?? null,
            severityLevel: $data['severityLevel'] ?? null,
            targetServices: $data['targetServices'] ?? null,
            strikeCount: $data['strikeCount'] ?? null,
            strikeExpiresAt: isset($data['strikeExpiresAt']) ? Carbon::parse($data['strikeExpiresAt']) : null
        );
    }

}
