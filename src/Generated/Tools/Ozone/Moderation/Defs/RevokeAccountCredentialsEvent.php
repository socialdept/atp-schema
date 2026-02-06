<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Account credentials revocation by moderators. Only works on DID subjects.
 *
 * Lexicon: tools.ozone.moderation.defs.revokeAccountCredentialsEvent
 * Type: object
 *
 * @property string $comment Comment describing the reason for the revocation.
 *
 * Constraints:
 * - Required: comment
 */
#[Generated(regenerate: true)]
class RevokeAccountCredentialsEvent extends Data
{
    /**
     * @param  string  $comment  Comment describing the reason for the revocation.
     */
    public function __construct(
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
        return 'tools.ozone.moderation.defs.revokeAccountCredentialsEvent';
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
            comment: $data['comment']
        );
    }

}
