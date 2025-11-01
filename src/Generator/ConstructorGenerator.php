<?php

namespace SocialDept\Schema\Generator;

class ConstructorGenerator
{
    /**
     * Property generator instance.
     */
    protected PropertyGenerator $propertyGenerator;

    /**
     * Stub renderer instance.
     */
    protected StubRenderer $renderer;

    /**
     * Create a new ConstructorGenerator.
     */
    public function __construct(
        ?PropertyGenerator $propertyGenerator = null,
        ?StubRenderer $renderer = null
    ) {
        $this->propertyGenerator = $propertyGenerator ?? new PropertyGenerator;
        $this->renderer = $renderer ?? new StubRenderer;
    }

    /**
     * Generate constructor with promoted properties.
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @param  array<string>  $required
     */
    public function generate(array $properties, array $required = []): string
    {
        if (empty($properties)) {
            return $this->generateEmpty();
        }

        $parameters = $this->generateParameters($properties, $required);
        $body = $this->generateBody($properties, $required);

        return $this->renderer->render('constructor', [
            'docBlock' => $this->generateDocBlock($properties, $required),
            'parameters' => $parameters,
            'body' => $body,
        ]);
    }

    /**
     * Generate empty constructor.
     */
    protected function generateEmpty(): string
    {
        return '';
    }

    /**
     * Generate constructor parameters.
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @param  array<string>  $required
     */
    protected function generateParameters(array $properties, array $required = []): string
    {
        $params = [];

        foreach ($properties as $name => $definition) {
            $promoted = $this->propertyGenerator->generatePromoted($name, $definition, $required);
            $params[] = '        '.$promoted.',';
        }

        // Remove trailing comma from last parameter
        if (! empty($params)) {
            $params[count($params) - 1] = rtrim($params[count($params) - 1], ',');
        }

        return implode("\n", $params);
    }

    /**
     * Generate constructor body.
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @param  array<string>  $required
     */
    protected function generateBody(array $properties, array $required = []): string
    {
        // For promoted properties, constructor body is usually empty
        // But we can add validation or initialization logic here if needed
        return '';
    }

    /**
     * Generate constructor documentation block.
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @param  array<string>  $required
     */
    protected function generateDocBlock(array $properties, array $required = []): string
    {
        if (empty($properties)) {
            return '';
        }

        $lines = ['    /**'];
        $lines[] = '     * Create a new instance.';

        // Add @param tags for each parameter
        foreach ($properties as $name => $definition) {
            $docType = $this->propertyGenerator->getDocType(
                $definition,
                ! in_array($name, $required)
            );
            $description = $definition['description'] ?? null;

            if ($description) {
                $lines[] = '     * @param  '.$docType.'  $'.$name.'  '.$description;
            } else {
                $lines[] = '     * @param  '.$docType.'  $'.$name;
            }
        }

        $lines[] = '     */';

        return implode("\n", $lines);
    }

    /**
     * Generate constructor with assignments (non-promoted).
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @param  array<string>  $required
     */
    public function generateWithAssignments(array $properties, array $required = []): string
    {
        if (empty($properties)) {
            return $this->generateEmpty();
        }

        $parameters = [];
        $assignments = [];

        foreach ($properties as $name => $definition) {
            $signature = $this->propertyGenerator->generateSignature($name, $definition, $required);
            $parameters[] = '        '.$signature.',';
            $assignments[] = '        $this->'.$name.' = $'.$name.';';
        }

        // Remove trailing comma from last parameter
        if (! empty($parameters)) {
            $parameters[count($parameters) - 1] = rtrim($parameters[count($parameters) - 1], ',');
        }

        $params = implode("\n", $parameters);
        $body = implode("\n", $assignments);

        return "    /**\n".
               "     * Create a new instance.\n".
               "     */\n".
               "    public function __construct(\n".
               $params."\n".
               "    ) {\n".
               $body."\n".
               "    }";
    }

    /**
     * Check if constructor should be generated.
     *
     * @param  array<string, array<string, mixed>>  $properties
     */
    public function shouldGenerate(array $properties): bool
    {
        return ! empty($properties);
    }
}
