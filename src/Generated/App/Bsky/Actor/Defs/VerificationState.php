<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\Defs\VerificationState\VerificationView;

/**
 * Represents the verification information about the user this object is
 * attached to.
 *
 * Lexicon: app.bsky.actor.defs.verificationState
 * Type: object
 *
 * @property array $verifications All verifications issued by trusted verifiers on behalf of this user. Verifications by untrusted verifiers are not included.
 * @property string $verifiedStatus The user's status as a verified account.
 * @property string $trustedVerifierStatus The user's status as a trusted verifier.
 *
 * Constraints:
 * - Required: verifications, verifiedStatus, trustedVerifierStatus
 */
class VerificationState extends Data
{

    /**
     * @param  array  $verifications  All verifications issued by trusted verifiers on behalf of this user. Verifications by untrusted verifiers are not included.
     * @param  string  $verifiedStatus  The user's status as a verified account.
     * @param  string  $trustedVerifierStatus  The user's status as a trusted verifier.
     */
    public function __construct(
        public readonly array $verifications,
        public readonly string $verifiedStatus,
        public readonly string $trustedVerifierStatus
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.verificationState';
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
            verifications: $data['verifications'] ?? [],
            verifiedStatus: $data['verifiedStatus'],
            trustedVerifierStatus: $data['trustedVerifierStatus']
        );
    }

}
