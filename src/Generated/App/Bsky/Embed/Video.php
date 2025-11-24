<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed;

use SocialDept\Schema\Data\BlobReference;
use SocialDept\Schema\Data\Data;

/**
 * A video embedded in a Bluesky record (eg, a post).
 *
 * Lexicon: app.bsky.embed.video
 * Type: object
 *
 * @property BlobReference $video The mp4 video file. May be up to 100mb, formerly limited to 50mb.
 * @property array<Caption>|null $captions
 * @property string|null $alt Alt text description of the video, for accessibility.
 * @property AspectRatio|null $aspectRatio
 *
 * Constraints:
 * - Required: video
 * - captions: Max length: 20
 * - alt: Max length: 10000
 * - alt: Max graphemes: 1000
 */
class Video extends Data
{

    /**
     * @param  BlobReference  $video  The mp4 video file. May be up to 100mb, formerly limited to 50mb.
     * @param  string|null  $alt  Alt text description of the video, for accessibility.
     */
    public function __construct(
        public readonly BlobReference $video,
        public readonly ?array $captions = null,
        public readonly ?string $alt = null,
        public readonly ?AspectRatio $aspectRatio = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.video';
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
            video: $data['video'],
            captions: $data['captions'] ?? [],
            alt: $data['alt'] ?? null,
            aspectRatio: isset($data['aspectRatio']) ? Defs::fromArray($data['aspectRatio']) : null
        );
    }

}
