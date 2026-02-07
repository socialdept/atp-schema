<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.modEventAcknowledge
 * Type: object
 *
 * @property string|null $comment
 * @property bool|null $acknowledgeAccountSubjects If true, all other reports on content authored by this account will be resolved (acknowledged).
 */
#[Generated(regenerate: true)]
class ModEventAcknowledge extends Data
{
    /**
     * @param  bool|null  $acknowledgeAccountSubjects  If true, all other reports on content authored by this account will be resolved (acknowledged).
     */
    public function __construct(
        public readonly ?string $comment = null,
        public readonly ?bool $acknowledgeAccountSubjects = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventAcknowledge';
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
            acknowledgeAccountSubjects: $data['acknowledgeAccountSubjects'] ?? null
        );
    }

}
