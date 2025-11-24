<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Label\SubscribeLabels;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Com\Atproto\Label\Label;

/**
 * Lexicon: com.atproto.label.subscribeLabels.labels
 * Type: object
 *
 * @property int $seq
 * @property array<Label> $labels
 *
 * Constraints:
 * - Required: seq, labels
 */
class Labels extends Data
{
    /**
     */
    public function __construct(
        public readonly int $seq,
        public readonly array $labels
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.label.subscribeLabels.labels';
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
            seq: $data['seq'],
            labels: isset($data['labels']) ? array_map(fn ($item) => Defs::fromArray($item), $data['labels']) : []
        );
    }

}
