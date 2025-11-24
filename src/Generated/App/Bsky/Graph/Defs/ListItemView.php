<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\ProfileView;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.graph.defs.listItemView
 * Type: object
 *
 * @property string $uri
 * @property ProfileView $subject
 *
 * Constraints:
 * - Required: uri, subject
 * - uri: Format: at-uri
 */
class ListItemView extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly ProfileView $subject
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.defs.listItemView';
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
            subject: Defs::fromArray($data['subject'])
        );
    }

}
