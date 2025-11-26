<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Add a comment to a subject. An empty comment will clear any previously set
 * sticky comment.
 *
 * Lexicon: tools.ozone.moderation.defs.modEventComment
 * Type: object
 *
 * @property string|null $comment
 * @property bool|null $sticky Make the comment persistent on the subject
 */
class ModEventComment extends Data
{
    /**
     * @param  bool|null  $sticky  Make the comment persistent on the subject
     */
    public function __construct(
        public readonly ?string $comment = null,
        public readonly ?bool $sticky = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventComment';
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
            comment: $data['comment'] ?? null,
            sticky: $data['sticky'] ?? null
        );
    }

}
