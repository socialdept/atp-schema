<?php

namespace SocialDept\AtpSchema\Parser;

use SocialDept\AtpSchema\Contracts\LexiconParser;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\SchemaParseException;

class DefaultLexiconParser implements LexiconParser
{
    /**
     * Parse raw Lexicon JSON into structured objects.
     */
    public function parse(string $json): LexiconDocument
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw SchemaParseException::invalidJson('unknown', json_last_error_msg());
        }

        if (! is_array($data)) {
            throw SchemaParseException::malformed('unknown', 'Schema must be a JSON object');
        }

        return $this->parseArray($data);
    }

    /**
     * Parse Lexicon from array data.
     */
    public function parseArray(array $data): LexiconDocument
    {
        return LexiconDocument::fromArray($data);
    }

    /**
     * Validate Lexicon schema structure.
     */
    public function validate(array $data): bool
    {
        try {
            // Required fields
            if (! isset($data['lexicon'])) {
                return false;
            }

            if (! isset($data['id'])) {
                return false;
            }

            if (! isset($data['defs'])) {
                return false;
            }

            // Validate lexicon version
            $lexicon = (int) $data['lexicon'];
            if ($lexicon !== 1) {
                return false;
            }

            // Validate NSID format
            Nsid::parse($data['id']);

            // Validate defs is an object/array
            if (! is_array($data['defs'])) {
                return false;
            }

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Resolve $ref references to other schemas.
     */
    public function resolveReference(string $ref, LexiconDocument $context): mixed
    {
        // Local reference (starting with #)
        if (str_starts_with($ref, '#')) {
            $defName = substr($ref, 1);

            return $context->getDefinition($defName);
        }

        // External reference with fragment (e.g., com.atproto.label.defs#selfLabels)
        if (str_contains($ref, '#')) {
            [$nsid, $defName] = explode('#', $ref, 2);

            // Return the ref as-is - external refs need schema loading which should be handled by caller
            return [
                'type' => 'ref',
                'ref' => $ref,
            ];
        }

        // Full NSID reference - return as ref definition
        return [
            'type' => 'ref',
            'ref' => $ref,
        ];
    }
}
