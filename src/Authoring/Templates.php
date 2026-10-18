<?php

namespace SocialDept\AtpSchema\Authoring;

use InvalidArgumentException;

/**
 * Starting points for new lexicons, one per primary type. Each passes the
 * linter, and every TODO marks something to write.
 */
class Templates
{
    public const TYPES = ['record', 'query', 'procedure', 'permission-set'];

    /**
     * @return array<string, mixed>
     */
    public static function make(string $type, string $nsid): array
    {
        $main = match ($type) {
            'record' => [
                'type' => 'record',
                'description' => 'TODO: what one of these records is.',
                'key' => 'tid',
                'record' => [
                    'type' => 'object',
                    'required' => ['createdAt'],
                    'properties' => [
                        'createdAt' => ['type' => 'string', 'format' => 'datetime'],
                    ],
                ],
            ],
            'query' => [
                'type' => 'query',
                'description' => 'TODO: what this endpoint answers.',
                'parameters' => [
                    'type' => 'params',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 50],
                        'cursor' => ['type' => 'string', 'maxLength' => 512],
                    ],
                ],
                'output' => [
                    'encoding' => 'application/json',
                    'schema' => [
                        'type' => 'object',
                        'required' => [],
                        'properties' => [
                            'cursor' => ['type' => 'string', 'maxLength' => 512],
                        ],
                    ],
                ],
            ],
            'procedure' => [
                'type' => 'procedure',
                'description' => 'TODO: what this endpoint does.',
                'input' => [
                    'encoding' => 'application/json',
                    'schema' => [
                        'type' => 'object',
                        'required' => ['subject'],
                        'properties' => [
                            'subject' => ['type' => 'string', 'format' => 'at-uri'],
                        ],
                    ],
                ],
                'output' => [
                    'encoding' => 'application/json',
                    'schema' => ['type' => 'object', 'properties' => (object) []],
                ],
            ],
            'permission-set' => [
                'type' => 'permission-set',
                'title' => 'TODO: a short name people see when granting it',
                'detail' => 'TODO: what an app holding these permissions can do',
                'permissions' => [
                    ['type' => 'permission', 'resource' => 'repo', 'collection' => ['TODO.example.record']],
                ],
            ],
            default => throw new InvalidArgumentException('Unknown lexicon type '.$type.'. Use one of: '.implode(', ', self::TYPES).'.'),
        };

        return ['lexicon' => 1, 'id' => $nsid, 'defs' => ['main' => $main]];
    }
}
