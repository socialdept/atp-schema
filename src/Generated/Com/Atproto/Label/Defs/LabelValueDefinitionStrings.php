<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Strings which describe the label in the UI, localized into a specific
 * language.
 *
 * Lexicon: com.atproto.label.defs.labelValueDefinitionStrings
 * Type: object
 *
 * @property string $lang The code of the language these strings are written in.
 * @property string $name A short human-readable name for the label.
 * @property string $description A longer description of what the label means and why it might be applied.
 *
 * Constraints:
 * - Required: lang, name, description
 * - lang: Format: language
 * - name: Max length: 640
 * - name: Max graphemes: 64
 * - description: Max length: 100000
 * - description: Max graphemes: 10000
 */
#[Generated(regenerate: true)]
class LabelValueDefinitionStrings extends Data
{
    /**
     * @param  string  $lang  The code of the language these strings are written in.
     * @param  string  $name  A short human-readable name for the label.
     * @param  string  $description  A longer description of what the label means and why it might be applied.
     */
    public function __construct(
        public readonly string $lang,
        public readonly string $name,
        public readonly string $description
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.label.defs.labelValueDefinitionStrings';
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
            name: $data['name'],
            description: $data['description']
        );
    }

}
