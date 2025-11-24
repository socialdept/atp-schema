<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: tools.ozone.moderation.defs.recordViewNotFound
 * Type: object
 *
 * @property string $uri
 *
 * Constraints:
 * - Required: uri
 * - uri: Format: at-uri
 */
class RecordViewNotFound extends Data
{
    /**
     */
    public function __construct(
        public readonly string $uri
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.recordViewNotFound';
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
            uri: $data['uri']
        );
    }

}
