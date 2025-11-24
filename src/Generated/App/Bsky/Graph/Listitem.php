<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.graph.listitem
 * Type: record
 */
class Listitem extends Data
{
    /**
     * @param  string  $subject  The account which is included on the list.
     * @param  string  $list  Reference (AT-URI) to the list record (app.bsky.graph.list).
     */
    public function __construct(
        public readonly string $subject,
        public readonly string $list,
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
        return 'app.bsky.graph.listitem';
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
            list: $data['list'],
            createdAt: Carbon::parse($data['createdAt'])
        );
    }

}
