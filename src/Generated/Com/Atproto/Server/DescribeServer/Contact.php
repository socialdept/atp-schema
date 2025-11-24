<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Server\DescribeServer;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.server.describeServer.contact
 * Type: object
 *
 * @property string|null $email
 */
class Contact extends Data
{

    public function __construct(
        public readonly ?string $email = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.server.describeServer.contact';
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
            email: $data['email'] ?? null
        );
    }

}
