<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed\Video;

use SocialDept\Schema\Data\BlobReference;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.embed.video.caption
 * Type: object
 *
 * @property string $lang
 * @property BlobReference $file
 *
 * Constraints:
 * - Required: lang, file
 * - lang: Format: language
 */
class Caption extends Data
{
    /**
     */
    public function __construct(
        public readonly string $lang,
        public readonly BlobReference $file
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.video.caption';
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
            lang: $data['lang'],
            file: $data['file']
        );
    }

}
