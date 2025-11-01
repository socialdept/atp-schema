<?php

namespace SocialDept\Schema\Facades;

use Illuminate\Support\Facades\Facade;
use SocialDept\Schema\Data\LexiconDocument;

/**
 * @method static array load(string $nsid)
 * @method static bool exists(string $nsid)
 * @method static LexiconDocument parse(string $nsid)
 * @method static bool validate(string $nsid, array $data)
 * @method static array validateWithErrors(string $nsid, array $data)
 * @method static string generate(string $nsid, array $options = [])
 * @method static void clearCache(?string $nsid = null)
 *
 * @see \SocialDept\Schema\SchemaManager
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
