<?php

namespace SocialDept\Schema\Generated\App\Bsky\Notification\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Notification\Defs\SubjectActivitySubscription\ActivitySubscription;

/**
 * Object used to store activity subscription data in stash.
 *
 * Lexicon: app.bsky.notification.defs.subjectActivitySubscription
 * Type: object
 *
 * @property string $subject
 * @property mixed $activitySubscription
 *
 * Constraints:
 * - Required: subject, activitySubscription
 * - subject: Format: did
 */
class SubjectActivitySubscription extends Data
{

    /**
     */
    public function __construct(
        public readonly string $subject,
        public readonly mixed $activitySubscription
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.notification.defs.subjectActivitySubscription';
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
            subject: $data['subject'],
            activitySubscription: $data['activitySubscription']
        );
    }

}
