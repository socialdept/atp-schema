<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Labeler\Defs;

use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\LabelValue;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\LabelValueDefinition;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.labeler.defs.labelerPolicies
 * Type: object
 *
 * @property array<LabelValue> $labelValues The label values which this labeler publishes. May include global or custom labels.
 * @property array<LabelValueDefinition>|null $labelValueDefinitions Label values created by this labeler and scoped exclusively to it. Labels defined here will override global label definitions for this labeler.
 *
 * Constraints:
 * - Required: labelValues
 */
class LabelerPolicies extends Data
{
    /**
     * @param  array<LabelValue>  $labelValues  The label values which this labeler publishes. May include global or custom labels.
     * @param  array<LabelValueDefinition>|null  $labelValueDefinitions  Label values created by this labeler and scoped exclusively to it. Labels defined here will override global label definitions for this labeler.
     */
    public function __construct(
        public readonly array $labelValues,
        public readonly ?array $labelValueDefinitions = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.labeler.defs.labelerPolicies';
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
            labelValues: isset($data['labelValues']) ? array_map(fn ($item) => LabelValue::fromArray($item), $data['labelValues']) : [],
            labelValueDefinitions: isset($data['labelValueDefinitions']) ? array_map(fn ($item) => LabelValueDefinition::fromArray($item), $data['labelValueDefinitions']) : []
        );
    }

}
