<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Notification\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.notification.defs.filterablePreference
 * Type: object
 *
 * @property string $include
 * @property bool $list
 * @property bool $push
 *
 * Constraints:
 * - Required: include, list, push
 */
#[Generated(regenerate: true)]
class FilterablePreference extends Data
{
    public function __construct(
        public readonly string $include,
        public readonly bool $list,
        public readonly bool $push
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.notification.defs.filterablePreference';
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
            include: $data['include'],
            list: $data['list'],
            push: $data['push']
        );
    }

}
