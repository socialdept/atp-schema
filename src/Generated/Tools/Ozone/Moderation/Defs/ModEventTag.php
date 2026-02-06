<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Add/Remove a tag on a subject
 *
 * Lexicon: tools.ozone.moderation.defs.modEventTag
 * Type: object
 *
 * @property array<string> $add Tags to be added to the subject. If already exists, won't be duplicated.
 * @property array<string> $remove Tags to be removed to the subject. Ignores a tag If it doesn't exist, won't be duplicated.
 * @property string|null $comment Additional comment about added/removed tags.
 *
 * Constraints:
 * - Required: add, remove
 */
#[Generated(regenerate: true)]
class ModEventTag extends Data
{
    /**
     * @param  array<string>  $add  Tags to be added to the subject. If already exists, won't be duplicated.
     * @param  array<string>  $remove  Tags to be removed to the subject. Ignores a tag If it doesn't exist, won't be duplicated.
     * @param  string|null  $comment  Additional comment about added/removed tags.
     */
    public function __construct(
        public readonly array $add,
        public readonly array $remove,
        public readonly ?string $comment = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventTag';
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
            add: $data['add'],
            remove: $data['remove'],
            comment: $data['comment'] ?? null
        );
    }

}
