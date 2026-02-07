<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Keep a log of outgoing email to a user
 *
 * Lexicon: tools.ozone.moderation.defs.modEventEmail
 * Type: object
 *
 * @property string $subjectLine The subject line of the email sent to the user.
 * @property string|null $content The content of the email sent to the user.
 * @property string|null $comment Additional comment about the outgoing comm.
 * @property array<string>|null $policies Names/Keywords of the policies that necessitated the email.
 * @property string|null $severityLevel Severity level of the violation. Normally 'sev-1' that adds strike on repeat offense
 * @property int|null $strikeCount Number of strikes to assign to the user for this violation. Normally 0 as an indicator of a warning and only added as a strike on a repeat offense.
 * @property Carbon|null $strikeExpiresAt When the strike should expire. If not provided, the strike never expires.
 * @property bool|null $isDelivered Indicates whether the email was successfully delivered to the user's inbox.
 *
 * Constraints:
 * - Required: subjectLine
 * - policies: Max length: 5
 * - strikeExpiresAt: Format: datetime
 */
#[Generated(regenerate: true)]
class ModEventEmail extends Data
{
    /**
     * @param  string  $subjectLine  The subject line of the email sent to the user.
     * @param  string|null  $content  The content of the email sent to the user.
     * @param  string|null  $comment  Additional comment about the outgoing comm.
     * @param  array<string>|null  $policies  Names/Keywords of the policies that necessitated the email.
     * @param  string|null  $severityLevel  Severity level of the violation. Normally 'sev-1' that adds strike on repeat offense
     * @param  int|null  $strikeCount  Number of strikes to assign to the user for this violation. Normally 0 as an indicator of a warning and only added as a strike on a repeat offense.
     * @param  Carbon|null  $strikeExpiresAt  When the strike should expire. If not provided, the strike never expires.
     * @param  bool|null  $isDelivered  Indicates whether the email was successfully delivered to the user's inbox.
     */
    public function __construct(
        public readonly string $subjectLine,
        public readonly ?string $content = null,
        public readonly ?string $comment = null,
        public readonly ?array $policies = null,
        public readonly ?string $severityLevel = null,
        public readonly ?int $strikeCount = null,
        public readonly ?Carbon $strikeExpiresAt = null,
        public readonly ?bool $isDelivered = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventEmail';
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
            subjectLine: $data['subjectLine'],
            content: $data['content'] ?? null,
            comment: $data['comment'] ?? null,
            policies: $data['policies'] ?? null,
            severityLevel: $data['severityLevel'] ?? null,
            strikeCount: $data['strikeCount'] ?? null,
            strikeExpiresAt: isset($data['strikeExpiresAt']) ? Carbon::parse($data['strikeExpiresAt']) : null,
            isDelivered: $data['isDelivered'] ?? null
        );
    }

}
