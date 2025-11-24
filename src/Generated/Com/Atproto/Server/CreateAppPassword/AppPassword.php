<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Server\CreateAppPassword;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.server.createAppPassword.appPassword
 * Type: object
 *
 * @property string $name
 * @property string $password
 * @property Carbon $createdAt
 * @property bool|null $privileged
 *
 * Constraints:
 * - Required: name, password, createdAt
 * - createdAt: Format: datetime
 */
class AppPassword extends Data
{

    public function __construct(
        public readonly string $name,
        public readonly string $password,
        public readonly Carbon $createdAt,
        public readonly ?bool $privileged = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.server.createAppPassword.appPassword';
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
            name: $data['name'],
            password: $data['password'],
            createdAt: Carbon::parse($data['createdAt']),
            privileged: $data['privileged'] ?? null
        );
    }

}
