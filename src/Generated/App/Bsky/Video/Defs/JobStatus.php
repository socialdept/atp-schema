<?php

namespace SocialDept\Schema\Generated\App\Bsky\Video\Defs;

use SocialDept\Schema\Data\BlobReference;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.video.defs.jobStatus
 * Type: object
 *
 * @property string $jobId
 * @property string $did
 * @property string $state The state of the video processing job. All values not listed as a known value indicate that the job is in process.
 * @property int|null $progress Progress within the current processing state.
 * @property BlobReference|null $blob
 * @property string|null $error
 * @property string|null $message
 *
 * Constraints:
 * - Required: jobId, did, state
 * - did: Format: did
 * - progress: Maximum: 100
 * - progress: Minimum: 0
 */
class JobStatus extends Data
{
    /**
     * @param  string  $state  The state of the video processing job. All values not listed as a known value indicate that the job is in process.
     * @param  int|null  $progress  Progress within the current processing state.
     */
    public function __construct(
        public readonly string $jobId,
        public readonly string $did,
        public readonly string $state,
        public readonly ?int $progress = null,
        public readonly ?BlobReference $blob = null,
        public readonly ?string $error = null,
        public readonly ?string $message = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.video.defs.jobStatus';
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
            jobId: $data['jobId'],
            did: $data['did'],
            state: $data['state'],
            progress: $data['progress'] ?? null,
            blob: $data['blob'] ?? null,
            error: $data['error'] ?? null,
            message: $data['message'] ?? null
        );
    }

}
