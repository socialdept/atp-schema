<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Revert take down action on a subject
 *
 * Lexicon: tools.ozone.moderation.defs.modEventReverseTakedown
 * Type: object
 *
 * @property string|null $comment Describe reasoning behind the reversal.
 * @property array<string>|null $policies Names/Keywords of the policy infraction for which takedown is being reversed.
 * @property string|null $severityLevel Severity level of the violation. Usually set from the last policy infraction's severity.
 * @property int|null $strikeCount Number of strikes to subtract from the user's strike count. Usually set from the last policy infraction's severity.
 *
 * Constraints:
 * - policies: Max length: 5
 */
#[Generated(regenerate: true)]
class ModEventReverseTakedown extends Data
{
    /**
     * @param  string|null  $comment  Describe reasoning behind the reversal.
     * @param  array<string>|null  $policies  Names/Keywords of the policy infraction for which takedown is being reversed.
     * @param  string|null  $severityLevel  Severity level of the violation. Usually set from the last policy infraction's severity.
     * @param  int|null  $strikeCount  Number of strikes to subtract from the user's strike count. Usually set from the last policy infraction's severity.
     */
    public function __construct(
        public readonly ?string $comment = null,
        public readonly ?array $policies = null,
        public readonly ?string $severityLevel = null,
        public readonly ?int $strikeCount = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventReverseTakedown';
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
            policies: $data['policies'] ?? null,
            severityLevel: $data['severityLevel'] ?? null,
            strikeCount: $data['strikeCount'] ?? null
        );
    }

}
