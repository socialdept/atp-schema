<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Server\DescribeServer;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.server.describeServer.links
 * Type: object
 *
 * @property string|null $privacyPolicy
 * @property string|null $termsOfService
 *
 * Constraints:
 * - privacyPolicy: Format: uri
 * - termsOfService: Format: uri
 */
class Links extends Data
{

    /**
     */
    public function __construct(
        public readonly ?string $privacyPolicy = null,
        public readonly ?string $termsOfService = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.server.describeServer.links';
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
            privacyPolicy: $data['privacyPolicy'] ?? null,
            termsOfService: $data['termsOfService'] ?? null
        );
    }

}
