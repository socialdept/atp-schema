<?php

namespace SocialDept\AtpSchema\Attributes;

use Attribute;

/**
 * Marks a class as auto-generated.
 *
 * When present, the generator will overwrite this file during regeneration.
 * To prevent regeneration, either:
 * - Remove this attribute entirely, or
 * - Set regenerate: false
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Generated
{
    public function __construct(
        public bool $regenerate = true
    ) {}
}
