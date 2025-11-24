<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Communication\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: tools.ozone.communication.defs.templateView
 * Type: object
 *
 * @property string $id
 * @property string $name Name of the template.
 * @property string|null $subject Content of the template, can contain markdown and variable placeholders.
 * @property string $contentMarkdown Subject of the message, used in emails.
 * @property bool $disabled
 * @property string|null $lang Message language.
 * @property string $lastUpdatedBy DID of the user who last updated the template.
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 *
 * Constraints:
 * - Required: id, name, contentMarkdown, disabled, lastUpdatedBy, createdAt, updatedAt
 * - lang: Format: language
 * - lastUpdatedBy: Format: did
 * - createdAt: Format: datetime
 * - updatedAt: Format: datetime
 */
class TemplateView extends Data
{

    /**
     * @param  string  $name  Name of the template.
     * @param  string  $contentMarkdown  Subject of the message, used in emails.
     * @param  string  $lastUpdatedBy  DID of the user who last updated the template.
     * @param  string|null  $subject  Content of the template, can contain markdown and variable placeholders.
     * @param  string|null  $lang  Message language.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $contentMarkdown,
        public readonly bool $disabled,
        public readonly string $lastUpdatedBy,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        public readonly ?string $subject = null,
        public readonly ?string $lang = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.communication.defs.templateView';
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
            id: $data['id'],
            name: $data['name'],
            contentMarkdown: $data['contentMarkdown'],
            disabled: $data['disabled'],
            lastUpdatedBy: $data['lastUpdatedBy'],
            createdAt: Carbon::parse($data['createdAt']),
            updatedAt: Carbon::parse($data['updatedAt']),
            subject: $data['subject'] ?? null,
            lang: $data['lang'] ?? null
        );
    }

}
