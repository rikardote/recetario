<?php

namespace App\Services\RmsV2;

use Symfony\Component\Yaml\Yaml;

/**
 * Parses and validates the YAML frontmatter header of an RMS v2.0 document.
 */
class YamlParser
{
    /**
     * Required fields in the YAML frontmatter.
     */
    private const REQUIRED_FIELDS = [
        'version', 'recipe_type', 'slug', 'title',
        'family', 'category', 'difficulty', 'servings',
        'prep_time', 'cook_time', 'release', 'total_time',
        'cost', 'author', 'language', 'status',
    ];

    /**
     * Valid recipe_type values.
     */
    private const VALID_RECIPE_TYPES = ['base', 'derived'];

    /**
     * Extract and validate YAML frontmatter from the document.
     *
     * Returns ['data' => [...], 'errors' => [...]].
     */
    public function parse(string $markdown): array
    {
        $errors = [];

        // Extract YAML between first pair of ---
        if (!preg_match('/^---\s*\n(.*?)\n---/s', $markdown, $m)) {
            $errors[] = 'rms-v2-header-missing: No se encontró el bloque YAML (debe comenzar con --- y terminar con ---).';
            return ['data' => null, 'errors' => $errors];
        }

        try {
            $data = Yaml::parse($m[1]);
        } catch (\Exception $e) {
            $errors[] = 'rms-v2-header-invalid: El YAML del encabezado no es válido: ' . $e->getMessage();
            return ['data' => null, 'errors' => $errors];
        }

        if (!is_array($data)) {
            $errors[] = 'rms-v2-header-empty: El bloque YAML está vacío.';
            return ['data' => null, 'errors' => $errors];
        }

        // Validate required fields
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!array_key_exists($field, $data)) {
                $errors[] = "rms-v2-header-missing-field: Falta el campo obligatorio «{$field}» en el YAML.";
            }
        }

        // Validate recipe_type
        if (isset($data['recipe_type']) && !in_array($data['recipe_type'], self::VALID_RECIPE_TYPES)) {
            $errors[] = "rms-v2-header-invalid-recipe-type: recipe_type debe ser 'base' o 'derived'. Se recibió: «{$data['recipe_type']}».";
        }

        // Validate version
        if (isset($data['version']) && (float) $data['version'] !== 2.0) {
            $errors[] = "rms-v2-header-version: La versión debe ser 2.0. Se recibió: «{$data['version']}».";
        }

        // Validate tags if present
        if (isset($data['tags'])) {
            if (!is_array($data['tags'])) {
                $errors[] = 'rms-v2-header-tags: El campo tags debe ser una lista.';
            }
        }

        // Validate difficulty is integer 1-5
        if (isset($data['difficulty'])) {
            $d = (int) $data['difficulty'];
            if ($d < 1 || $d > 5) {
                $errors[] = "rms-v2-header-difficulty: difficulty debe ser un número entre 1 y 5. Se recibió: «{$data['difficulty']}».";
            } else {
                $data['difficulty'] = $d;
            }
        }

        // Validate servings is integer
        if (isset($data['servings'])) {
            $data['servings'] = (int) $data['servings'];
        }

        // Parse prep_time, cook_time, total_time as minutes
        foreach (['prep_time', 'cook_time', 'total_time'] as $timeField) {
            if (isset($data[$timeField])) {
                $data[$timeField] = $this->parseMinutes($data[$timeField]);
            }
        }

        // Normalize release
        if (isset($data['release'])) {
            $release = strtolower((string) $data['release']);
            if (str_contains($release, 'natural')) {
                $data['release'] = 'natural';
            } elseif (str_contains($release, 'rapida') || str_contains($release, 'rápida')) {
                $data['release'] = 'rapida';
            }
            // If unknown, keep as is
        }

        return ['data' => $data, 'errors' => $errors];
    }

    private function parseMinutes(mixed $val): int
    {
        if (is_numeric($val)) return (int) $val;
        if (preg_match('/(\d+)/', (string) $val, $m)) return (int) $m[1];
        return 0;
    }
}
