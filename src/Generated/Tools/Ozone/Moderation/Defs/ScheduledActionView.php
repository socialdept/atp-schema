<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * View of a scheduled moderation action
 *
 * Lexicon: tools.ozone.moderation.defs.scheduledActionView
 * Type: object
 *
 * @property int $id Auto-incrementing row ID
 * @property string $action Type of action to be executed
 * @property mixed $eventData Serialized event object that will be propagated to the event when performed
 * @property string $did Subject DID for the action
 * @property Carbon|null $executeAt Exact time to execute the action
 * @property Carbon|null $executeAfter Earliest time to execute the action (for randomized scheduling)
 * @property Carbon|null $executeUntil Latest time to execute the action (for randomized scheduling)
 * @property bool|null $randomizeExecution Whether execution time should be randomized within the specified range
 * @property string $createdBy DID of the user who created this scheduled action
 * @property Carbon $createdAt When the scheduled action was created
 * @property Carbon|null $updatedAt When the scheduled action was last updated
 * @property string $status Current status of the scheduled action
 * @property Carbon|null $lastExecutedAt When the action was last attempted to be executed
 * @property string|null $lastFailureReason Reason for the last execution failure
 * @property int|null $executionEventId ID of the moderation event created when action was successfully executed
 *
 * Constraints:
 * - Required: id, action, did, createdBy, createdAt, status
 * - did: Format: did
 * - executeAt: Format: datetime
 * - executeAfter: Format: datetime
 * - executeUntil: Format: datetime
 * - createdBy: Format: did
 * - createdAt: Format: datetime
 * - updatedAt: Format: datetime
 * - lastExecutedAt: Format: datetime
 */
class ScheduledActionView extends Data
{
    /**
     * @param  int  $id  Auto-incrementing row ID
     * @param  string  $action  Type of action to be executed
     * @param  string  $did  Subject DID for the action
     * @param  string  $createdBy  DID of the user who created this scheduled action
     * @param  Carbon  $createdAt  When the scheduled action was created
     * @param  string  $status  Current status of the scheduled action
     * @param  mixed  $eventData  Serialized event object that will be propagated to the event when performed
     * @param  Carbon|null  $executeAt  Exact time to execute the action
     * @param  Carbon|null  $executeAfter  Earliest time to execute the action (for randomized scheduling)
     * @param  Carbon|null  $executeUntil  Latest time to execute the action (for randomized scheduling)
     * @param  bool|null  $randomizeExecution  Whether execution time should be randomized within the specified range
     * @param  Carbon|null  $updatedAt  When the scheduled action was last updated
     * @param  Carbon|null  $lastExecutedAt  When the action was last attempted to be executed
     * @param  string|null  $lastFailureReason  Reason for the last execution failure
     * @param  int|null  $executionEventId  ID of the moderation event created when action was successfully executed
     */
    public function __construct(
        public readonly int $id,
        public readonly string $action,
        public readonly string $did,
        public readonly string $createdBy,
        public readonly Carbon $createdAt,
        public readonly string $status,
        public readonly mixed $eventData = null,
        public readonly ?Carbon $executeAt = null,
        public readonly ?Carbon $executeAfter = null,
        public readonly ?Carbon $executeUntil = null,
        public readonly ?bool $randomizeExecution = null,
        public readonly ?Carbon $updatedAt = null,
        public readonly ?Carbon $lastExecutedAt = null,
        public readonly ?string $lastFailureReason = null,
        public readonly ?int $executionEventId = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.scheduledActionView';
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
            action: $data['action'],
            did: $data['did'],
            createdBy: $data['createdBy'],
            createdAt: Carbon::parse($data['createdAt']),
            status: $data['status'],
            eventData: $data['eventData'] ?? null,
            executeAt: isset($data['executeAt']) ? Carbon::parse($data['executeAt']) : null,
            executeAfter: isset($data['executeAfter']) ? Carbon::parse($data['executeAfter']) : null,
            executeUntil: isset($data['executeUntil']) ? Carbon::parse($data['executeUntil']) : null,
            randomizeExecution: $data['randomizeExecution'] ?? null,
            updatedAt: isset($data['updatedAt']) ? Carbon::parse($data['updatedAt']) : null,
            lastExecutedAt: isset($data['lastExecutedAt']) ? Carbon::parse($data['lastExecutedAt']) : null,
            lastFailureReason: $data['lastFailureReason'] ?? null,
            executionEventId: $data['executionEventId'] ?? null
        );
    }

}
