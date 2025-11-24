<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed\Record;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.embed.record.viewNotFound
 * Type: object
 *
 * @property string $uri
 * @property bool $notFound
 *
 * Constraints:
 * - Required: uri, notFound
 * - uri: Format: at-uri
 * - notFound: Const: true
 */
class ViewNotFound extends Data
{

    public function __construct(
        public readonly string $uri,
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
        return 'app.bsky.embed.record.viewNotFound';
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
            notFound: $data['notFound']
        );
    }

}
