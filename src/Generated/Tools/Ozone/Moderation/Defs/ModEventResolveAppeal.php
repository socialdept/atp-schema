<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Resolve appeal on a subject
 *
 * Lexicon: tools.ozone.moderation.defs.modEventResolveAppeal
 * Type: object
 *
 * @property string|null $comment Describe resolution.
 */
class ModEventResolveAppeal extends Data
{

    /**
     * @param  string|null  $comment  Describe resolution.
     */
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
        return 'tools.ozone.moderation.defs.modEventResolveAppeal';
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
