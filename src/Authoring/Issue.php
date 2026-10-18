<?php

namespace SocialDept\AtpSchema\Authoring;

/**
 * One finding about a lexicon, from linting or from comparing two versions.
 *
 * Names and levels follow Bluesky's own lexicon tooling (goat lex), so a
 * finding reads the same whichever tool reported it.
 */
final class Issue
{
    public const ERROR = 'error';

    public const WARN = 'warn';

    public const INFO = 'info';

    public function __construct(
        public readonly string $nsid,
        public readonly string $level,
        public readonly string $name,
        public readonly string $message,
    ) {
    }

    public static function error(string $nsid, string $name, string $message): self
    {
        return new self($nsid, self::ERROR, $name, $message);
    }

    public static function warn(string $nsid, string $name, string $message): self
    {
        return new self($nsid, self::WARN, $name, $message);
    }

    public static function info(string $nsid, string $name, string $message): self
    {
        return new self($nsid, self::INFO, $name, $message);
    }

    public function isError(): bool
    {
        return $this->level === self::ERROR;
    }

    /**
     * @param  array<int, self>  $issues
     */
    public static function anyErrors(array $issues): bool
    {
        foreach ($issues as $issue) {
            if ($issue->isError()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{nsid: string, level: string, name: string, message: string}
     */
    public function toArray(): array
    {
        return ['nsid' => $this->nsid, 'level' => $this->level, 'name' => $this->name, 'message' => $this->message];
    }
}
