<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.contentLabelPref
 * Type: object
 *
 * @property string|null $labelerDid Which labeler does this preference apply to? If undefined, applies globally.
 * @property string $label
 * @property string $visibility
 *
 * Constraints:
 * - Required: label, visibility
 * - labelerDid: Format: did
 */
#[Generated(regenerate: true)]
class ContentLabelPref extends Data
{
    /**
     * @param  string|null  $labelerDid  Which labeler does this preference apply to? If undefined, applies globally.
     */
    public function __construct(
        public readonly string $label,
        public readonly string $visibility,
        public readonly ?string $labelerDid = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.contentLabelPref';
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
            label: $data['label'],
            visibility: $data['visibility'],
            labelerDid: $data['labelerDid'] ?? null
        );
    }

}
