<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\MutedWordTarget;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * A word that the account owner has muted.
 *
 * Lexicon: app.bsky.actor.defs.mutedWord
 * Type: object
 *
 * @property string|null $id
 * @property string $value The muted word itself.
 * @property array<MutedWordTarget> $targets The intended targets of the muted word.
 * @property string|null $actorTarget Groups of users to apply the muted word to. If undefined, applies to all users.
 * @property Carbon|null $expiresAt The date and time at which the muted word will expire and no longer be applied.
 *
 * Constraints:
 * - Required: value, targets
 * - value: Max length: 10000
 * - value: Max graphemes: 1000
 * - expiresAt: Format: datetime
 */
class MutedWord extends Data
{
    /**
     * @param  string  $value  The muted word itself.
     * @param  array<MutedWordTarget>  $targets  The intended targets of the muted word.
     * @param  string|null  $actorTarget  Groups of users to apply the muted word to. If undefined, applies to all users.
     * @param  Carbon|null  $expiresAt  The date and time at which the muted word will expire and no longer be applied.
     */
    public function __construct(
        public readonly string $value,
        public readonly array $targets,
        public readonly ?string $id = null,
        public readonly ?string $actorTarget = null,
        public readonly ?Carbon $expiresAt = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.mutedWord';
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
            value: $data['value'],
            targets: isset($data['targets']) ? array_map(fn ($item) => Defs::fromArray($item), $data['targets']) : [],
            id: $data['id'] ?? null,
            actorTarget: $data['actorTarget'] ?? null,
            expiresAt: isset($data['expiresAt']) ? Carbon::parse($data['expiresAt']) : null
        );
    }

}
