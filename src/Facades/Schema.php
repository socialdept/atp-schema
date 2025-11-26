<?php

namespace SocialDept\AtpSchema\Facades;

use Illuminate\Support\Facades\Facade;
use SocialDept\AtpSchema\Data\LexiconDocument;

/**
 * @method static LexiconDocument load(string $nsid)
 * @method static LexiconDocument|null find(string $nsid)
 * @method static bool exists(string $nsid)
 * @method static array all()
 * @method static void clearCache(?string $nsid = null)
 * @method static string generate(string $nsid, ?string $outputPath = null)
 * @method static bool validate(string $nsid, array $data)
 * @method static array validateWithErrors(string $nsid, array $data)
 *
 * @see \SocialDept\AtpSchema\SchemaManager
 */
class Schema extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'schema';
    }
}
