<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * An individual verification for an associated subject.
 *
 * Lexicon: app.bsky.actor.defs.verificationView
 * Type: object
 *
 * @property string $issuer The user who issued this verification.
 * @property string $uri The AT-URI of the verification record.
 * @property bool $isValid True if the verification passes validation, otherwise false.
 * @property Carbon $createdAt Timestamp when the verification was created.
 *
 * Constraints:
 * - Required: issuer, uri, isValid, createdAt
 * - issuer: Format: did
 * - uri: Format: at-uri
 * - createdAt: Format: datetime
 */
class VerificationView extends Data
{

    /**
     * @param  string  $issuer  The user who issued this verification.
     * @param  string  $uri  The AT-URI of the verification record.
     * @param  bool  $isValid  True if the verification passes validation, otherwise false.
     * @param  Carbon  $createdAt  Timestamp when the verification was created.
     */
    public function __construct(
        public readonly string $issuer,
        public readonly string $uri,
        public readonly bool $isValid,
        public readonly Carbon $createdAt
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.verificationView';
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
            issuer: $data['issuer'],
            uri: $data['uri'],
            isValid: $data['isValid'],
            createdAt: Carbon::parse($data['createdAt'])
        );
    }

}
