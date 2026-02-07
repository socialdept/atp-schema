<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Server\CreateInviteCodes;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.server.createInviteCodes.accountCodes
 * Type: object
 *
 * @property string $account
 * @property array<string> $codes
 *
 * Constraints:
 * - Required: account, codes
 */
#[Generated(regenerate: true)]
class AccountCodes extends Data
{
    public function __construct(
        public readonly string $account,
        public readonly array $codes
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.server.createInviteCodes.accountCodes';
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
            account: $data['account'],
            codes: $data['codes']
        );
    }

}
