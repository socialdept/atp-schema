<?php

namespace SocialDept\AtpSchema\Support;

class PathHelper
{
    /**
     * Convert a relative path to a PHP namespace.
     *
     * Examples:
     *   'app/Lexicons' → 'App\Lexicons'
     *   'app/Services/Clients' → 'App\Services\Clients'
     */
    public static function pathToNamespace(string $path): string
    {
        return collect(explode('/', $path))
            ->map(fn (string $segment) => ucfirst($segment))
            ->implode('\\');
    }
}
