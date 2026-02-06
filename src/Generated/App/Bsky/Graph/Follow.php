<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Graph;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\Com\Atproto\Repo\StrongRef;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.graph.follow
 * Type: record
 */
#[Generated(regenerate: true)]
class Follow extends Data
{
    public function __construct(
        public readonly string $subject,
        public readonly Carbon $createdAt,
        public readonly ?StrongRef $via = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.follow';
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
            subject: $data['subject'],
            createdAt: Carbon::parse($data['createdAt']),
            via: isset($data['via']) ? StrongRef::fromArray($data['via']) : null
        );
    }

}
