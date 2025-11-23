<?php

namespace SocialDept\Schema\Parser;

use SocialDept\Schema\Exceptions\SchemaException;
use Stringable;

class Nsid implements Stringable
{
    /**
     * NSID pattern: authority.name (reversed domain notation)
     */
    protected const NSID_REGEX = '/^[a-zA-Z]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/';

    /**
     * Maximum NSID length
     */
    protected const MAX_LENGTH = 317;

    /**
     * Minimum NSID segments
     */
    protected const MIN_SEGMENTS = 3;

    /**
     * Create a new NSID instance.
     */
    public function __construct(
        protected string $nsid
    ) {
        $this->validate();
    }

    /**
     * Parse NSID from string.
     */
    public static function parse(string $nsid): self
    {
        return new self($nsid);
    }

    /**
     * Validate NSID format.
     */
    protected function validate(): void
    {
        if (empty($this->nsid)) {
            throw SchemaException::withContext('NSID cannot be empty', ['nsid' => $this->nsid]);
        }

        if (strlen($this->nsid) > self::MAX_LENGTH) {
            throw SchemaException::withContext(
                'NSID exceeds maximum length of '.self::MAX_LENGTH.' characters',
                ['nsid' => $this->nsid, 'length' => strlen($this->nsid)]
            );
        }

        if (! preg_match(self::NSID_REGEX, $this->nsid)) {
            throw SchemaException::withContext(
                'Invalid NSID format. Expected reversed domain notation (e.g., app.bsky.feed.post)',
                ['nsid' => $this->nsid]
            );
        }

        $segments = explode('.', $this->nsid);
        if (count($segments) < self::MIN_SEGMENTS) {
            throw SchemaException::withContext(
                'NSID must have at least '.self::MIN_SEGMENTS.' segments',
                ['nsid' => $this->nsid, 'segments' => count($segments)]
            );
        }
    }

    /**
     * Get the authority (all segments except the last).
     */
    public function getAuthority(): string
    {
        $segments = explode('.', $this->nsid);
        array_pop($segments);

        return implode('.', $segments);
    }

    /**
     * Get the name (last segment).
     */
    public function getName(): string
    {
        $segments = explode('.', $this->nsid);

        return end($segments);
    }

    /**
     * Get all segments.
     *
     * @return array<string>
     */
    public function getSegments(): array
    {
        return explode('.', $this->nsid);
    }

    /**
     * Convert to standard domain format (reverse segments).
     */
    public function toDomain(): string
    {
        $segments = $this->getSegments();

        return implode('.', array_reverse($segments));
    }

    /**
     * Get the NSID string.
     */
    public function toString(): string
    {
        return $this->nsid;
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Check if NSID is valid (static method).
     */
    public static function isValid(string $nsid): bool
    {
        try {
            new self($nsid);

            return true;
        } catch (SchemaException) {
            return false;
        }
    }

    /**
     * Check equality with another NSID.
     */
    public function equals(self $other): bool
    {
        return $this->nsid === $other->nsid;
    }

    /**
     * Get the authority domain for DNS lookup.
     * Returns the authority segments in DNS order (reversed).
     */
    public function getAuthorityDomain(): string
    {
        $authoritySegments = explode('.', $this->getAuthority());

        return implode('.', array_reverse($authoritySegments));
    }
}
