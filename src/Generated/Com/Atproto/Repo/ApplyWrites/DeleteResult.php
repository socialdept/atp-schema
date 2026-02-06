<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Repo\ApplyWrites;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.repo.applyWrites.deleteResult
 * Type: object
 */
#[Generated(regenerate: true)]
class DeleteResult extends Data
{
    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.repo.applyWrites.deleteResult';
    }


    /**
     * Create an instance from an array.
     *
     * @param  array  $data  The data array
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static();
    }

}
