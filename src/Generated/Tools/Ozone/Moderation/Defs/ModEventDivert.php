<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Divert a record's blobs to a 3rd party service for further scanning/tagging
 *
 * Lexicon: tools.ozone.moderation.defs.modEventDivert
 * Type: object
 *
 * @property string|null $comment
 */
#[Generated(regenerate: true)]
class ModEventDivert extends Data
{
    public function __construct(
        public readonly ?string $comment = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventDivert';
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
            comment: $data['comment'] ?? null
        );
    }

}
