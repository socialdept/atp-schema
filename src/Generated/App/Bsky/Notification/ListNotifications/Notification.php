<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Notification\ListNotifications;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ProfileView;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\Label;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.notification.listNotifications.notification
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property ProfileView $author
 * @property string $reason The reason why this notification was delivered - e.g. your post was liked, or you received a new follower.
 * @property string|null $reasonSubject
 * @property mixed $record
 * @property bool $isRead
 * @property Carbon $indexedAt
 * @property array<Label>|null $labels
 *
 * Constraints:
 * - Required: uri, cid, author, reason, record, isRead, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - reasonSubject: Format: at-uri
 * - indexedAt: Format: datetime
 */
class Notification extends Data
{
    /**
     * @param  string  $reason  The reason why this notification was delivered - e.g. your post was liked, or you received a new follower.
     */
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly ProfileView $author,
        public readonly string $reason,
        public readonly mixed $record,
        public readonly bool $isRead,
        public readonly Carbon $indexedAt,
        public readonly ?string $reasonSubject = null,
        public readonly ?array $labels = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.notification.listNotifications.notification';
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
            uri: $data['uri'],
            cid: $data['cid'],
            author: ProfileView::fromArray($data['author']),
            reason: $data['reason'],
            record: $data['record'],
            isRead: $data['isRead'],
            indexedAt: Carbon::parse($data['indexedAt']),
            reasonSubject: $data['reasonSubject'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : []
        );
    }

}
