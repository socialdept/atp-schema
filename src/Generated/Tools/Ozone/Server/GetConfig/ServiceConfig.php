<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Server\GetConfig;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.server.getConfig.serviceConfig
 * Type: object
 *
 * @property string|null $url
 *
 * Constraints:
 * - url: Format: uri
 */
#[Generated(regenerate: true)]
class ServiceConfig extends Data
{
    public function __construct(
        public readonly ?string $url = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.server.getConfig.serviceConfig';
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
            url: $data['url'] ?? null
        );
    }

}
