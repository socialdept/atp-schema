<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph\Defs;

use SocialDept\Schema\Data\Data;

/**
 * lists the bi-directional graph relationships between one actor (not indicated
 * in the object), and the target actors (the DID included in the object)
 *
 * Lexicon: app.bsky.graph.defs.relationship
 * Type: object
 *
 * @property string $did
 * @property string|null $following if the actor follows this DID, this is the AT-URI of the follow record
 * @property string|null $followedBy if the actor is followed by this DID, contains the AT-URI of the follow record
 *
 * Constraints:
 * - Required: did
 * - did: Format: did
 * - following: Format: at-uri
 * - followedBy: Format: at-uri
 */
class Relationship extends Data
{

    /**
     * @param  string|null  $following  if the actor follows this DID, this is the AT-URI of the follow record
     * @param  string|null  $followedBy  if the actor is followed by this DID, contains the AT-URI of the follow record
     */
    public function __construct(
        public readonly string $did,
        public readonly ?string $following = null,
        public readonly ?string $followedBy = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.defs.relationship';
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
            did: $data['did'],
            following: $data['following'] ?? null,
            followedBy: $data['followedBy'] ?? null
        );
    }

}
