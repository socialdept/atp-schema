<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Label\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Declares a label value and its expected interpretations and behaviors.
 *
 * Lexicon: com.atproto.label.defs.labelValueDefinition
 * Type: object
 *
 * @property string $identifier The value of the label being defined. Must only include lowercase ascii and the '-' character ([a-z-]+).
 * @property string $severity How should a client visually convey this label? 'inform' means neutral and informational; 'alert' means negative and warning; 'none' means show nothing.
 * @property string $blurs What should this label hide in the UI, if applied? 'content' hides all of the target; 'media' hides the images/video/audio; 'none' hides nothing.
 * @property string|null $defaultSetting The default setting for this label.
 * @property bool|null $adultOnly Does the user need to have adult content enabled in order to configure this label?
 * @property array $locales
 *
 * Constraints:
 * - Required: identifier, severity, blurs, locales
 * - identifier: Max length: 100
 * - identifier: Max graphemes: 100
 */
class LabelValueDefinition extends Data
{

    /**
     * @param  string  $identifier  The value of the label being defined. Must only include lowercase ascii and the '-' character ([a-z-]+).
     * @param  string  $severity  How should a client visually convey this label? 'inform' means neutral and informational; 'alert' means negative and warning; 'none' means show nothing.
     * @param  string  $blurs  What should this label hide in the UI, if applied? 'content' hides all of the target; 'media' hides the images/video/audio; 'none' hides nothing.
     * @param  string|null  $defaultSetting  The default setting for this label.
     * @param  bool|null  $adultOnly  Does the user need to have adult content enabled in order to configure this label?
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $severity,
        public readonly string $blurs,
        public readonly array $locales,
        public readonly ?string $defaultSetting = null,
        public readonly ?bool $adultOnly = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.label.defs.labelValueDefinition';
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
            identifier: $data['identifier'],
            severity: $data['severity'],
            blurs: $data['blurs'],
            locales: $data['locales'] ?? [],
            defaultSetting: $data['defaultSetting'] ?? null,
            adultOnly: $data['adultOnly'] ?? null
        );
    }

}
