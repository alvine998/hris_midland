<?php

namespace App\Services\Ai;

use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

class FunctionRegistry
{
    /**
     * Registered functions indexed by name.
     *
     * Each entry contains:
     *  - 'name'        (string) – function identifier used by the AI
     *  - 'description' (string) – what the function does
     *  - 'permission'  (string) – required permission to invoke
     *  - 'parameters'  (array)  – parameter definitions from WAFunctionParam attributes
     *  - 'class'       (string) – fully qualified class name
     *  - 'method'      (string) – method name on the class
     *
     * @var array<string, array{name: string, description: string, permission: string, parameters: array, class: string, method: string}>
     */
    protected array $functions = [];

    /**
     * Register a single function class by fully-qualified class name.
     *
     * Scans all public methods on the given class for the #[WAFunction]
     * attribute and registers each annotated method as a callable function.
     *
     * If a function with the same name is already registered, the new
     * definition overwrites the old one.
     *
     * @throws RuntimeException When the class does not exist.
     * @throws \ReflectionException
     */
    public function register(string $class): void
    {
        if (! class_exists($class)) {
            throw new RuntimeException("Class [{$class}] does not exist.");
        }

        $reflection = new ReflectionClass($class);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $attributes = $method->getAttributes(WAFunction::class);

            if (empty($attributes)) {
                continue;
            }

            /** @var WAFunction $attribute */
            $attribute = $attributes[0]->newInstance();

            $parameters = $this->extractParameters($method);

            // Convert to JSON Schema format for AI tool declarations
            $jsonSchema = $this->parametersToJsonSchema($parameters);

            $this->functions[$attribute->name] = [
                'name' => $attribute->name,
                'description' => $attribute->description,
                'permission' => $attribute->permission,
                'parameters' => $jsonSchema,
                'class' => $class,
                'method' => $method->getName(),
            ];
        }
    }

    /**
     * Scan a directory for PHP classes and register every function class found.
     *
     * Expects files to follow the PSR-4 naming convention relative to the
     * application base path.  For example, a file at
     * `app/Services/Ai/Functions/GetWeather.php` is resolved to the class
     * `App\Services\Ai\Functions\GetWeather`.
     *
     * Directories that do not exist are silently ignored.
     *
     * @throws \ReflectionException
     */
    public function scanDirectory(string $path): void
    {
        $path = rtrim($path, '/');

        if (! is_dir($path)) {
            return;
        }

        $files = glob($path.'/*.php');

        if ($files === false || empty($files)) {
            return;
        }

        foreach ($files as $file) {
            $class = $this->resolveClassFromFile($file);

            if ($class === null) {
                continue;
            }

            try {
                $this->register($class);
            } catch (RuntimeException) {
                // Class not autoloadable — skip silently.
                continue;
            }
        }
    }

    /**
     * Find the top-N most relevant functions for a user message using
     * keyword overlap scoring.
     *
     * Tokenizes both the user message and each function's description,
     * then scores by the count of matching tokens.  Results are sorted
     * descending by score.  When no keyword matches are found (all scores
     * are zero), the first N registered functions are returned as a
     * fallback.
     *
     * @param  string  $userMessage  The raw user input to search against.
     * @param  int  $limit  Maximum number of results to return.
     * @return array<int, array{name: string, description: string, permission: string, parameters: array, class: string, method: string, score: int}>
     */
    public function searchFunctions(string $userMessage, int $limit = 5): array
    {
        if (empty($this->functions)) {
            return [];
        }

        $queryTokens = $this->tokenize($userMessage);

        // If the message is empty or contains only punctuation, return the
        // first N functions as a reasonable default.
        if (empty($queryTokens)) {
            return array_map(
                fn (array $fn) => $fn + ['score' => 0],
                array_slice(array_values($this->functions), 0, $limit),
            );
        }

        $scored = [];

        foreach ($this->functions as $function) {
            $descriptionTokens = $this->tokenize($function['description']);
            $score = 0;

            foreach ($queryTokens as $token) {
                if (in_array($token, $descriptionTokens, true)) {
                    $score++;
                }
            }

            $scored[] = $function + ['score' => $score];
        }

        // Sort by score descending.
        usort($scored, fn (array $a, array $b) => $b['score'] <=> $a['score']);

        // If the top score is still zero, fall back to the first N functions.
        if ($scored[0]['score'] === 0) {
            return array_map(
                fn (array $fn) => $fn + ['score' => 0],
                array_slice(array_values($this->functions), 0, $limit),
            );
        }

        return array_slice($scored, 0, $limit);
    }

    /**
     * Get a single function definition by its registered name.
     *
     * @return array{name: string, description: string, permission: string, parameters: array, class: string, method: string}|null
     */
    public function getFunction(string $name): ?array
    {
        return $this->functions[$name] ?? null;
    }

    /**
     * Get all registered functions.
     *
     * @return array<string, array{name: string, description: string, permission: string, parameters: array, class: string, method: string}>
     */
    public function getAllFunctions(): array
    {
        return $this->functions;
    }

    /**
     * Convert an array of function definitions to the Gemini tool declaration
     * format that can be passed directly to GeminiClient::buildFunctionDeclaration().
     *
     * Each input entry should contain at least 'name', 'description', and 'parameters'.
     * The 'parameters' value is an array of per-parameter arrays with keys:
     *  - 'name'        (string) – parameter name
     *  - 'type'        (string) – parameter type (e.g. 'string', 'number')
     *  - 'description' (string) – parameter description
     *  - 'enum'        (array)  – optional list of allowed values
     *  - 'required'    (bool)   – whether the parameter is required
     *
     * @param  array<int, array{name: string, description: string, parameters: array}>  $functions
     * @return array<int, array{name: string, description: string, parameters: array{type: string, properties: array, required: array}}>
     */
    public function toGeminiDeclarations(array $functions): array
    {
        $declarations = [];

        foreach ($functions as $function) {
            $params = $function['parameters'] ?? [];
            $properties = [];
            $required = [];

            foreach ($params as $param) {
                $property = [
                    'type' => $param['type'] ?? 'string',
                    'description' => $param['description'] ?? '',
                ];

                if (! empty($param['enum'])) {
                    $property['enum'] = $param['enum'];
                }

                $properties[$param['name']] = $property;

                if (! empty($param['required'])) {
                    $required[] = $param['name'];
                }
            }

            $declarations[] = [
                'name' => $function['name'],
                'description' => $function['description'] ?? '',
                'parameters' => [
                    'type' => 'object',
                    'properties' => $properties,
                    'required' => $required,
                ],
            ];
        }

        return $declarations;
    }

    /**
     * Extract parameter definitions from a method's WAFunctionParam attributes.
     *
     * Iterates over the method's formal parameters and reads the
     * #[WAFunctionParam] attribute from each one.
     *
     * @return array<int, array{name: string, type: string, description: string, enum: array, required: bool}>
     */
    private function extractParameters(ReflectionMethod $method): array
    {
        $parameters = [];

        foreach ($method->getParameters() as $reflectionParameter) {
            $attributes = $reflectionParameter->getAttributes(WAFunctionParam::class);

            if (empty($attributes)) {
                continue;
            }

            /** @var WAFunctionParam $paramAttr */
            $paramAttr = $attributes[0]->newInstance();

            $parameters[] = [
                'name' => $paramAttr->name,
                'type' => $paramAttr->type,
                'description' => $paramAttr->description,
                'enum' => $paramAttr->enum,
                'required' => $paramAttr->required,
            ];
        }

        return $parameters;
    }

    /**
     * Convert an array of parameter definitions to JSON Schema format.
     *
     * @param  array<int, array{name: string, type: string, description: string, enum: array, required: bool}>  $parameters
     * @return array{type: string, properties: array, required: array}
     */
    private function parametersToJsonSchema(array $parameters): array
    {
        $properties = [];
        $required = [];

        foreach ($parameters as $param) {
            $property = [
                'type' => $param['type'] ?? 'string',
                'description' => $param['description'] ?? '',
            ];

            if (! empty($param['enum'])) {
                $property['enum'] = $param['enum'];
            }

            $properties[$param['name']] = $property;

            if (! empty($param['required'])) {
                $required[] = $param['name'];
            }
        }

        return [
            'type' => 'object',
            'properties' => empty($properties) ? new \stdClass() : $properties,
            'required' => $required,
        ];
    }

    /**
     * Tokenize a string into a set of lowercase alphanumeric tokens.
     *
     * Punctuation is removed and the string is split on whitespace.
     * Empty tokens are discarded.
     *
     * @return array<int, string>
     */
    private function tokenize(string $text): array
    {
        $text = Str::lower($text);
        $text = preg_replace('/[^\w\s]/u', '', $text);

        return array_values(
            array_filter(
                explode(' ', $text),
                fn (string $token) => $token !== '',
            ),
        );
    }

    /**
     * Resolve a fully-qualified class name from a file path.
     *
     * Expects the file to reside within the application's `app/` directory.
     * The path is converted to a PSR-4 class name under the `App\` root
     * namespace.
     *
     * Returns null when the path does not contain an `/app/` segment.
     */
    private function resolveClassFromFile(string $filePath): ?string
    {
        // Normalise directory separators to forward slashes.
        $path = str_replace('\\', '/', $filePath);

        // Locate the '/app/' portion of the absolute path.
        $pos = strpos($path, '/app/');

        if ($pos === false) {
            return null;
        }

        // Extract everything after '/app/' and strip the '.php' extension.
        $relative = substr($path, $pos + 5);
        $relative = Str::replaceLast('.php', '', $relative);

        // Convert directory separators to namespace separators.
        $class = str_replace('/', '\\', $relative);

        return 'App\\'.$class;
    }
}
