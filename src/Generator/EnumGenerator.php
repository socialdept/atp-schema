<?php

namespace SocialDept\Schema\Generator;

class EnumGenerator
{
    /**
     * Naming converter for class/enum names.
     */
    protected NamingConverter $naming;

    /**
     * File writer for writing generated files.
     */
    protected FileWriter $fileWriter;

    /**
     * Base namespace for generated enums.
     */
    protected string $baseNamespace;

    /**
     * Output directory for generated files.
     */
    protected string $outputDirectory;

    /**
     * Create a new EnumGenerator.
     */
    public function __construct(
        string $baseNamespace = 'App\\Lexicons',
        string $outputDirectory = 'app/Lexicons',
        ?NamingConverter $naming = null,
        ?FileWriter $fileWriter = null
    ) {
        $this->baseNamespace = rtrim($baseNamespace, '\\');
        $this->outputDirectory = rtrim($outputDirectory, '/');
        $this->naming = $naming ?? new NamingConverter($baseNamespace);
        $this->fileWriter = $fileWriter ?? new FileWriter();
    }

    /**
     * Generate PHP enum from a string type with knownValues.
     *
     * @param  string  $nsid  The NSID (e.g., "com.atproto.moderation.defs#reasonType")
     * @param  array  $definition  The lexicon definition
     * @return string The generated enum code
     */
    public function generate(string $nsid, array $definition): string
    {
        $type = $definition['type'] ?? null;

        if ($type !== 'string' || ! isset($definition['knownValues'])) {
            throw new \InvalidArgumentException("Definition must be a string type with knownValues");
        }

        // Extract namespace and enum name from NSID
        [$baseNsid, $defName] = $this->parseNsid($nsid);

        $namespace = $this->naming->nsidToNamespace($baseNsid);
        $enumName = $this->naming->toClassName($defName);

        $description = $definition['description'] ?? '';
        $knownValues = $definition['knownValues'];

        // Generate enum cases
        $cases = $this->generateCases($knownValues);

        return $this->renderEnum($namespace, $enumName, $description, $cases);
    }

    /**
     * Parse NSID into base NSID and definition name.
     *
     * @return array{0: string, 1: string}
     */
    protected function parseNsid(string $nsid): array
    {
        if (str_contains($nsid, '#')) {
            [$baseNsid, $defName] = explode('#', $nsid, 2);

            return [$baseNsid, $defName];
        }

        // If no fragment, use the last part of the NSID as the enum name
        $parts = explode('.', $nsid);
        $defName = array_pop($parts);
        $baseNsid = implode('.', $parts);

        return [$baseNsid, $defName];
    }

    /**
     * Generate enum cases from known values.
     *
     * @param  array<string>  $knownValues
     * @return array<array{name: string, value: string}>
     */
    protected function generateCases(array $knownValues): array
    {
        $cases = [];
        $usedNames = [];

        foreach ($knownValues as $value) {
            // Extract the case name from the value
            // e.g., "com.atproto.moderation.defs#reasonSpam" -> "REASON_SPAM"
            $caseName = $this->valueToCaseName($value);

            // Handle duplicate case names by prepending the source namespace
            if (isset($usedNames[$caseName])) {
                // Get the source namespace (e.g., "tools.ozone.report" from "tools.ozone.report.defs#reasonAppeal")
                if (str_contains($value, '#')) {
                    $nsid = explode('#', $value)[0];
                    $parts = explode('.', $nsid);
                    // Use the second-to-last part as a differentiator (e.g., "Ozone", "Report")
                    $diff = ucfirst($parts[count($parts) - 2] ?? $parts[count($parts) - 1]);
                    $caseName = $diff . $caseName;
                }
            }

            $usedNames[$caseName] = true;
            $cases[] = [
                'name' => $caseName,
                'value' => $value,
            ];
        }

        return $cases;
    }

    /**
     * Convert a known value to an enum case name.
     */
    protected function valueToCaseName(string $value): string
    {
        // If it's an NSID reference, extract the fragment part
        if (str_contains($value, '#')) {
            $value = explode('#', $value)[1];
        }

        // Remove leading symbols (!, etc.)
        $value = ltrim($value, '!@#$%^&*()-_=+[]{}|;:,.<>?/~`');

        // Convert kebab-case and snake_case to PascalCase
        // e.g., "no-promote" -> "NoPromote", "dmca-violation" -> "DmcaViolation"
        $value = str_replace(['-', '_'], ' ', $value);
        $value = ucwords($value);
        $value = str_replace(' ', '', $value);

        // Ensure first character is uppercase
        return ucfirst($value);
    }

    /**
     * Render the enum code.
     *
     * @param  array<array{name: string, value: string}>  $cases
     */
    protected function renderEnum(string $namespace, string $enumName, string $description, array $cases): string
    {
        $code = "<?php\n\n";
        $code .= "namespace {$namespace};\n\n";

        if ($description) {
            $code .= "/**\n";
            $code .= " * " . str_replace("\n", "\n * ", $description) . "\n";
            $code .= " */\n";
        }

        $code .= "enum {$enumName}: string\n";
        $code .= "{\n";

        foreach ($cases as $case) {
            $code .= "    case {$case['name']} = '{$case['value']}';\n";
        }

        $code .= "}\n";

        return $code;
    }

    /**
     * Generate and save enum to disk.
     */
    public function generateAndSave(string $nsid, array $definition): string
    {
        $code = $this->generate($nsid, $definition);

        [$baseNsid, $defName] = $this->parseNsid($nsid);
        $namespace = $this->naming->nsidToNamespace($baseNsid);
        $enumName = $this->naming->toClassName($defName);

        $filePath = $this->getFilePath($namespace, $enumName);
        $this->fileWriter->write($filePath, $code);

        return $filePath;
    }

    /**
     * Get the file path for a generated enum.
     */
    protected function getFilePath(string $namespace, string $enumName): string
    {
        // Remove base namespace from full namespace
        $relativePath = str_replace($this->baseNamespace.'\\', '', $namespace);
        $relativePath = str_replace('\\', '/', $relativePath);

        return $this->outputDirectory.'/'.$relativePath.'/'.$enumName.'.php';
    }
}
