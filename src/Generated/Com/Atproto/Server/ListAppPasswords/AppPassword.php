<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Server\ListAppPasswords;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.server.listAppPasswords.appPassword
 * Type: object
 *
 * @property string $name
 * @property Carbon $createdAt
 * @property bool|null $privileged
 *
 * Constraints:
 * - Required: name, createdAt
 * - createdAt: Format: datetime
 */
#[Generated(regenerate: true)]
class AppPassword extends Data
{
    public function __construct(
        public readonly string $name,
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
        return 'com.atproto.server.listAppPasswords.appPassword';
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
            createdAt: Carbon::parse($data['createdAt']),
            privileged: $data['privileged'] ?? null
        );
    }

}
