<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Set priority score of the subject. Higher score means higher priority.
 *
 * Lexicon: tools.ozone.moderation.defs.modEventPriorityScore
 * Type: object
 *
 * @property string|null $comment
 * @property int $score
 *
 * Constraints:
 * - Required: score
 * - score: Maximum: 100
 * - score: Minimum: 0
 */
class ModEventPriorityScore extends Data
{

    /**
     */
    public function __construct(
        public readonly int $score,
        public readonly ?string $comment = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventPriorityScore';
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
            score: $data['score'],
            comment: $data['comment'] ?? null
        );
    }

}
