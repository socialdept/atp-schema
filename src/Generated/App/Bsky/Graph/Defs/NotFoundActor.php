<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph\Defs;

use SocialDept\Schema\Data\Data;

/**
 * indicates that a handle or DID could not be resolved
 *
 * Lexicon: app.bsky.graph.defs.notFoundActor
 * Type: object
 *
 * @property string $actor
 * @property bool $notFound
 *
 * Constraints:
 * - Required: actor, notFound
 * - actor: Format: at-identifier
 * - notFound: Const: true
 */
class NotFoundActor extends Data
{
    /**
     */
    public function __construct(
        public readonly string $actor,
        public readonly bool $notFound
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.defs.notFoundActor';
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
            actor: $data['actor'],
            notFound: $data['notFound']
        );
    }

}
