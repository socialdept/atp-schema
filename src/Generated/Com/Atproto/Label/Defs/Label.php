<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Metadata tag on an atproto resource (eg, repo or record).
 *
 * Lexicon: com.atproto.label.defs.label
 * Type: object
 *
 * @property int|null $ver The AT Protocol version of the label object.
 * @property string $src DID of the actor who created this label.
 * @property string $uri AT URI of the record, repository (account), or other resource that this label applies to.
 * @property string|null $cid Optionally, CID specifying the specific version of 'uri' resource this label applies to.
 * @property string $val The short string name of the value or type of this label.
 * @property bool|null $neg If true, this is a negation label, overwriting a previous label.
 * @property Carbon $cts Timestamp when this label was created.
 * @property Carbon|null $exp Timestamp at which this label expires (no longer applies).
 * @property string|null $sig Signature of dag-cbor encoded label.
 *
 * Constraints:
 * - Required: src, uri, val, cts
 * - src: Format: did
 * - uri: Format: uri
 * - cid: Format: cid
 * - val: Max length: 128
 * - cts: Format: datetime
 * - exp: Format: datetime
 */
class Label extends Data
{
    /**
     * @param  string  $src  DID of the actor who created this label.
     * @param  string  $uri  AT URI of the record, repository (account), or other resource that this label applies to.
     * @param  string  $val  The short string name of the value or type of this label.
     * @param  Carbon  $cts  Timestamp when this label was created.
     * @param  int|null  $ver  The AT Protocol version of the label object.
     * @param  string|null  $cid  Optionally, CID specifying the specific version of 'uri' resource this label applies to.
     * @param  bool|null  $neg  If true, this is a negation label, overwriting a previous label.
     * @param  Carbon|null  $exp  Timestamp at which this label expires (no longer applies).
     * @param  string|null  $sig  Signature of dag-cbor encoded label.
     */
    public function __construct(
        public readonly string $src,
        public readonly string $uri,
        public readonly string $val,
        public readonly Carbon $cts,
        public readonly ?int $ver = null,
        public readonly ?string $cid = null,
        public readonly ?bool $neg = null,
        public readonly ?Carbon $exp = null,
        public readonly ?string $sig = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.label.defs.label';
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
            src: $data['src'],
            uri: $data['uri'],
            val: $data['val'],
            cts: Carbon::parse($data['cts']),
            ver: $data['ver'] ?? null,
            cid: $data['cid'] ?? null,
            neg: $data['neg'] ?? null,
            exp: isset($data['exp']) ? Carbon::parse($data['exp']) : null,
            sig: $data['sig'] ?? null
        );
    }

}
