<?php

namespace SocialDept\Schema\Contracts;

/**
 * Contract for Data classes that can participate in AT Protocol discriminated unions.
 *
 * Union types in AT Protocol use the $type field to discriminate between
 * different variants. This interface marks classes that can be used as
 * union variants and provides access to their discriminator value.
 */
interface DiscriminatedUnion
{
    /**
     * Get the lexicon NSID that identifies this union variant.
     *
     * This value is used as the $type discriminator in AT Protocol records
     * to identify which specific type a union contains.
     */
    public static function getDiscriminator(): string;
}
