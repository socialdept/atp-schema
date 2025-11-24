<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Apply/Negate labels on a subject
 *
 * Lexicon: tools.ozone.moderation.defs.modEventLabel
 * Type: object
 *
 * @property string|null $comment
 * @property array<string> $createLabelVals
 * @property array<string> $negateLabelVals
 * @property int|null $durationInHours Indicates how long the label will remain on the subject. Only applies on labels that are being added.
 *
 * Constraints:
 * - Required: createLabelVals, negateLabelVals
 */
class ModEventLabel extends Data
{

    /**
     * @param  int|null  $durationInHours  Indicates how long the label will remain on the subject. Only applies on labels that are being added.
     */
    public function __construct(
        public readonly array $createLabelVals,
        public readonly array $negateLabelVals,
        public readonly ?string $comment = null,
        public readonly ?int $durationInHours = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventLabel';
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
            createLabelVals: $data['createLabelVals'],
            negateLabelVals: $data['negateLabelVals'],
            comment: $data['comment'] ?? null,
            durationInHours: $data['durationInHours'] ?? null
        );
    }

}
