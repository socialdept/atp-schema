<?php

namespace SocialDept\Schema\Generator;

use SocialDept\Schema\Exceptions\GenerationException;

class TemplateRenderer
{
    /**
     * Templates directory.
     */
    protected ?string $templatesDirectory = null;

    /**
     * In-memory templates.
     *
     * @var array<string, string>
     */
    protected array $templates = [];

    /**
     * Create a new TemplateRenderer.
     */
    public function __construct(?string $templatesDirectory = null)
    {
        $this->templatesDirectory = $templatesDirectory;
        $this->registerDefaultTemplates();
    }

    /**
     * Render a template with given data.
     */
    public function render(string $templateName, array $data): string
    {
        $template = $this->getTemplate($templateName);

        return $this->renderTemplate($template, $data);
    }

    /**
     * Get template content.
     */
    protected function getTemplate(string $templateName): string
    {
        // Check in-memory templates first
        if (isset($this->templates[$templateName])) {
            return $this->templates[$templateName];
        }

        // Check templates directory
        if ($this->templatesDirectory !== null) {
            $path = $this->templatesDirectory.'/'.$templateName.'.php.template';

            if (file_exists($path)) {
                return file_get_contents($path);
            }
        }

        throw GenerationException::templateNotFound($templateName);
    }

    /**
     * Render template with data.
     */
    protected function renderTemplate(string $template, array $data): string
    {
        // Simple variable replacement
        foreach ($data as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $template = str_replace("{{{$key}}}", (string) $value, $template);
            }
        }

        // Handle property lists
        if (isset($data['properties']) && is_array($data['properties'])) {
            $template = $this->renderProperties($template, $data['properties']);
        }

        return $template;
    }

    /**
     * Render properties section.
     */
    protected function renderProperties(string $template, array $properties): string
    {
        $propertiesCode = [];

        foreach ($properties as $prop) {
            $typeHint = $prop['phpType'];
            $nullable = ! ($prop['required'] ?? true);

            if ($nullable && $typeHint !== 'mixed') {
                $typeHint = '?'.$typeHint;
            }

            $docComment = '';
            if ($prop['description'] ?? null) {
                $docComment = "    /**\n     * {$prop['description']}\n     */\n";
            }

            $propertiesCode[] = sprintf(
                '%s    public readonly %s $%s;',
                $docComment,
                $typeHint,
                $prop['name']
            );
        }

        $propertiesString = implode("\n\n", $propertiesCode);

        return str_replace('{{properties}}', $propertiesString, $template);
    }

    /**
     * Register default templates.
     */
    protected function registerDefaultTemplates(): void
    {
        $this->templates['record'] = <<<'PHP'
<?php

namespace {{namespace}};

/**
 * {{description}}
 *
 * NSID: {{nsid}}
 */
class {{className}}
{
{{properties}}

    /**
     * Create a new {{className}}.
     */
    public function __construct(
        // Constructor parameters will be generated
    ) {
        // Property assignments will be generated
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            // Assignments will be generated
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            // Array conversion will be generated
        ];
    }
}

PHP;

        $this->templates['object'] = <<<'PHP'
<?php

namespace {{namespace}};

/**
 * {{description}}
 */
class {{className}}
{
{{properties}}

    /**
     * Create a new {{className}}.
     */
    public function __construct(
        // Constructor parameters will be generated
    ) {
        // Property assignments will be generated
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            // Assignments will be generated
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            // Array conversion will be generated
        ];
    }
}

PHP;
    }

    /**
     * Register a custom template.
     */
    public function registerTemplate(string $name, string $template): void
    {
        $this->templates[$name] = $template;
    }

    /**
     * Set templates directory.
     */
    public function setTemplatesDirectory(string $directory): void
    {
        $this->templatesDirectory = $directory;
    }
}
