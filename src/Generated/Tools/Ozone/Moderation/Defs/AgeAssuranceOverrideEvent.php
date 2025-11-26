<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Age assurance status override by moderators. Only works on DID subjects.
 *
 * Lexicon: tools.ozone.moderation.defs.ageAssuranceOverrideEvent
 * Type: object
 *
 * @property string $status The status to be set for the user decided by a moderator, overriding whatever value the user had previously. Use reset to default to original state.
 * @property string $comment Comment describing the reason for the override.
 *
 * Constraints:
 * - Required: comment, status
 */
class AgeAssuranceOverrideEvent extends Data
{
    /**
     * @param  string  $status  The status to be set for the user decided by a moderator, overriding whatever value the user had previously. Use reset to default to original state.
     * @param  string  $comment  Comment describing the reason for the override.
     */
    public function __construct(
        public readonly string $status,
        public readonly string $comment
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.ageAssuranceOverrideEvent';
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
            status: $data['status'],
            comment: $data['comment']
        );
    }

}
