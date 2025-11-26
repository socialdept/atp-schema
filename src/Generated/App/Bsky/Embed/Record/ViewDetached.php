<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\Record;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.record.viewDetached
 * Type: object
 *
 * @property string $uri
 * @property bool $detached
 *
 * Constraints:
 * - Required: uri, detached
 * - uri: Format: at-uri
 * - detached: Const: true
 */
class ViewDetached extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly bool $detached
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.record.viewDetached';
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
            uri: $data['uri'],
            detached: $data['detached']
        );
    }

}
