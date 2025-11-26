<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Server\GetConfig;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.server.getConfig.viewerConfig
 * Type: object
 *
 * @property string|null $role
 */
class ViewerConfig extends Data
{
    public function __construct(
        public readonly ?string $role = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.server.getConfig.viewerConfig';
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
            role: $data['role'] ?? null
        );
    }

}
