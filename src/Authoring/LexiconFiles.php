<?php

namespace SocialDept\AtpSchema\Authoring;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

/**
 * The app's own lexicon files, one per NSID, laid out the way SchemaLoader
 * reads them: com.example.thing lives at com/example/thing.json.
 */
class LexiconFiles
{
    public function __construct(
        protected string $root,
    ) {
    }

    public static function fromConfig(): self
    {
        $path = config('atp-schema.lexicon_path') ?? (config('atp-schema.sources')[0] ?? resource_path('lexicons'));

        return new self(rtrim((string) $path, '/'));
    }

    public function root(): string
    {
        return $this->root;
    }

    public function path(string $nsid): string
    {
        return $this->root.'/'.str_replace('.', '/', $nsid).'.json';
    }

    public function exists(string $nsid): bool
    {
        return is_file($this->path($nsid));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RuntimeException when the file is missing or not JSON
     */
    public function read(string $nsid): array
    {
        return $this->decode($this->path($nsid));
    }

    /**
     * @param  array<string, mixed>  $lexicon
     */
    public function write(string $nsid, array $lexicon): string
    {
        $path = $this->path($nsid);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, self::encode($lexicon));

        return $path;
    }

    /**
     * Every lexicon file under the root, by the NSID it declares. A file that
     * is not JSON or declares no id is keyed by its path instead.
     *
     * @return array<string, string> NSID or path => file path
     */
    public function all(): array
    {
        if (! is_dir($this->root)) {
            return [];
        }

        $files = [];

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            try {
                $id = $this->decode($file->getPathname())['id'] ?? null;
            } catch (RuntimeException) {
                $id = null;
            }

            $files[is_string($id) && $id !== '' ? $id : $file->getPathname()] = $file->getPathname();
        }

        ksort($files);

        return $files;
    }

    /**
     * @return array<string, mixed>
     */
    public function decode(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("No lexicon file at {$path}.");
        }

        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data)) {
            throw new RuntimeException("{$path} is not valid JSON: ".json_last_error_msg());
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $lexicon
     */
    public static function encode(array $lexicon): string
    {
        return json_encode($lexicon, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
    }
}
