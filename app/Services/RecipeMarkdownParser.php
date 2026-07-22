<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeAdaptation;
use App\Models\RecipeConcept;
use App\Models\RecipeError;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\RecipeVariant;
use App\Models\Tag;
use Illuminate\Support\Str;

/**
 * Parser for the Recetario markdown format v1.0
 *
 * Format uses YAML-like frontmatter, --- section separators,
 * and structured sub-sections with ###.
 */
class RecipeMarkdownParser
{
    private string $md;
    private array $result = [];
    private array $warnings = [];
    private array $sections = [];

    public function __construct(string $markdown)
    {
        $this->md = $markdown;
    }

    /**
     * Parse markdown into structured array (no DB writes).
     */
    public function parse(): array
    {
        $this->result = [];
        $this->warnings = [];
        $this->sections = $this->splitSections();

        $this->parseHeader();
        $this->parseSection('Objetivo', fn($s) => $this->parseObjective($s));
        $this->parseSection('Ingredientes', fn($s) => $this->parseIngredients($s));
        $this->parseSection('Equipo', fn($s) => $this->parseEquipment($s));
        $this->parseSection('Preparación previa', fn($s) => $this->parsePreparacion($s));
        $this->parseSection('Preparacion previa', fn($s) => $this->parsePreparacion($s));
        $this->parseSection('Procedimiento', fn($s) => $this->parseProcedure($s));
        $this->parseSection('Resultado esperado', fn($s) => $this->parseResultado($s));
        $this->parseSection('Variantes', fn($s) => $this->parseVariants($s));
        $this->parseSection('Adaptaciones', fn($s) => $this->parseAdaptations($s));
        $this->parseSection('Conservación', fn($s) => $this->parseConservacion($s));
        $this->parseSection('Resumen técnico', fn($s) => $this->parseResumenTecnico($s));
        $this->parseSection('Conceptos aprendidos', fn($s) => $this->parseConcepts($s));
        $this->parseSection('Problemas frecuentes', fn($s) => $this->parseProblemas($s));
        $this->parseSection('Notas técnicas', fn($s) => $this->parseChefNotes($s));

        return [
            'result' => $this->result,
            'warnings' => $this->warnings,
        ];
    }

    /**
     * Parse and import into database.
     */
    public function import(): Recipe
    {
        $this->parse();
        $data = $this->result;

        $catName = $data['category'] ?? ($data['categories'][0] ?? 'Sin categoría');
        $catNames = $data['categories'] ?? [$catName];

        // Resolve/create all categories, track primary
        $catIds = [];
        $primaryId = null;
        foreach ($catNames as $i => $name) {
            $name = trim($name);
            $cat = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'icon' => $this->categoryIcon($name)]
            );
            $catIds[] = $cat->id;
            if ($i === 0) $primaryId = $cat->id;
        }

        $slug = $data['slug'] ?? Str::slug($data['name'] ?? 'sin-nombre');

        // Asegurar slug único
        $baseSlug = $slug;
        $counter = 1;
        while (Recipe::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $recipe = Recipe::create([
            'name' => $data['name'] ?? 'Sin nombre',
            'slug' => $slug,
            'recipe_type' => $data['recipe_type'] ?? 'base',
            'description' => $data['description'] ?? ($data['objective'] ?? ''),
            'objective' => $data['objective'] ?? '',
            'prep_time' => $data['prep_time'] ?? 0,
            'cook_time' => $data['cook_time'] ?? 0,
            'total_time' => $data['total_time'] ?? 0,
            'servings' => $data['servings'] ?? 4,
            'difficulty' => $data['difficulty'] ?? 1,
            'cost' => $data['cost'] ?? 'Medio',
            'result_texture' => $data['result_texture'] ?? null,
            'result_color' => $data['result_color'] ?? null,
            'result_consistency' => $data['result_consistency'] ?? null,
            'result_flavor' => $data['result_flavor'] ?? null,
            'storage_refrigeration' => $data['storage_refrigeration'] ?? null,
            'storage_freezing' => $data['storage_freezing'] ?? null,
            'storage_reheating' => $data['storage_reheating'] ?? null,
            'pressure_cook_time' => $data['pressure_cook_time'] ?? null,
            'pressure_release' => $data['pressure_release'] ?? null,
            'pressure_release_time' => $data['pressure_release_time'] ?? null,
            'saute_time' => $data['saute_time'] ?? null,
            'chef_notes' => $data['chef_notes'] ?? null,
            'is_published' => true,
        ]);

        // Sync categories (primary = first in list)
        $recipe->syncCategories($catIds, $primaryId);

        // Sync dependencies (for derived recipes)
        if (!empty($data['dependencies'])) {
            $recipe->syncDependencies($data['dependencies']);
        }

        // Tags
        if (!empty($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['slug' => Str::slug($tagName)], ['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $recipe->tags()->sync($tagIds);
        }

        // Equipment
        if (!empty($data['equipment'])) {
            $eqIds = [];
            foreach ($data['equipment'] as $eqName) {
                $clean = str_replace(' (Opcional)', '', $eqName);
                $clean = str_replace(' (opcional)', '', $clean);
                $eq = Equipment::firstOrCreate(
                    ['slug' => Str::slug($clean)],
                    ['name' => $clean, 'description' => $clean]
                );
                $eqIds[] = $eq->id;
            }
            $recipe->equipment()->sync($eqIds);
        }

        // Ingredients
        if (!empty($data['ingredients'])) {
            foreach ($data['ingredients'] as $ing) {
                $ingredient = Ingredient::firstOrCreate(
                    ['slug' => Str::slug($ing['name'])],
                    ['name' => $ing['name']]
                );
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'category' => $ing['category'],
                    'quantity' => $ing['quantity'],
                    'unit' => $ing['unit'],
                    'is_optional' => $ing['is_optional'] ?? false,
                    'is_recommended' => !($ing['is_optional'] ?? false),
                    'notes' => $ing['notes'] ?? null,
                ]);
            }
        }

        // Steps (prepend preparation as step 0 if present)
        $allSteps = $data['steps'] ?? [];
        if (!empty($data['preparation'])) {
            array_unshift($allSteps, [
                'action' => "🔪 Preparación previa\n\n" . $data['preparation'],
                'technical_fundament' => '',
                'what_to_observe' => null,
                'common_errors' => null,
            ]);
        }
        if (!empty($allSteps)) {
            foreach ($allSteps as $i => $step) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'step_number' => $i + 1,
                    'action' => $step['action'] ?? '',
                    'technical_fundament' => $step['technical_fundament'] ?? '',
                    'what_to_observe' => $step['what_to_observe'] ?? null,
                    'common_errors' => $step['common_errors'] ?? null,
                ]);
            }
        }

        // Variants
        if (!empty($data['variants'])) {
            foreach ($data['variants'] as $v) {
                RecipeVariant::create([
                    'recipe_id' => $recipe->id,
                    'name' => $v['name'],
                    'description' => $v['description'] ?? null,
                    'ingredients_changes' => $v['ingredients_changes'] ?? null,
                    'procedure_changes' => $v['procedure_changes'] ?? null,
                ]);
            }
        }

        // Adaptations
        if (!empty($data['adaptations'])) {
            foreach ($data['adaptations'] as $a) {
                RecipeAdaptation::create([
                    'recipe_id' => $recipe->id,
                    'scenario' => $a['scenario'],
                    'adaptation_text' => $a['text'],
                ]);
            }
        }

        // Concepts
        if (!empty($data['concepts'])) {
            foreach ($data['concepts'] as $c) {
                RecipeConcept::create([
                    'recipe_id' => $recipe->id,
                    'concept_text' => $c,
                ]);
            }
        }

        // Errors
        if (!empty($data['errors'])) {
            foreach ($data['errors'] as $e) {
                RecipeError::create([
                    'recipe_id' => $recipe->id,
                    'problem' => $e['problem'],
                    'possible_cause' => $e['possible_cause'] ?? null,
                    'solution' => $e['solution'] ?? '',
                ]);
            }
        }

        return $recipe;
    }

    // ═══════════════════════════════════════════════════════════
    // Section splitting (by --- separators)
    // ═══════════════════════════════════════════════════════════

    private function splitSections(): array
    {
        // Remove YAML frontmatter (first --- block)
        $content = $this->md;
        $content = preg_replace('/^---\n.*?\n---\n/su', '', $content);

        // Split remaining by ---
        $blocks = preg_split('/\n---+\n/', $content);

        $sections = [];
        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            // First line usually has the header
            $lines = explode("\n", $block);
            $first = trim($lines[0], "# \t");

            // Detect section type - recipe header has inline YAML keys
            $isHeader = false;
            foreach ($lines as $line) {
                if (preg_match('/^(slug|category|difficulty|servings|prep_time|cook_time|release|total_time|cost|instant_pot|tags):/', trim($line))) {
                    $isHeader = true;
                    break;
                }
            }

            if ($isHeader) {
                $sections['__header__'] = $block;
                continue;
            }

            $key = strtolower($first);
            $sections[$key] = $block;
        }

        // Merge orphan "paso N" blocks back into procedimiento
        $this->mergeOrphanSteps($sections);

        return $sections;
    }

    /**
     * Merge orphan "paso N" blocks back into procedimiento.
     */
    private function mergeOrphanSteps(array &$sections): void
    {
        $procedimiento = $sections['procedimiento'] ?? '';
        $toMerge = [];

        foreach ($sections as $key => $block) {
            if (preg_match('/^paso\s+\d+/', $key)) {
                $toMerge[] = $key;
                $procedimiento .= "\n\n" . $block;
            }
        }

        if (!empty($toMerge)) {
            $sections['procedimiento'] = $procedimiento;
            foreach ($toMerge as $key) {
                unset($sections[$key]);
            }
        }
    }

    private function parseSection(string $name, callable $callback): void
    {
        $key = strtolower($name);
        if (isset($this->sections[$key])) {
            $callback($this->sections[$key]);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Header (recipe metadata block)
    // ═══════════════════════════════════════════════════════════

    private function parseHeader(): void
    {
        $block = $this->sections['__header__'] ?? '';
        if (empty($block)) return;

        // Parse YAML-like key: value pairs
        $lines = explode("\n", $block);

        // First line is the name
        if (preg_match('/^#\s*(?:[🍗🐷🥩🐟🦐🍝🍚🍜🍳🍰🍞🥦🧀🍵🥣🧆🌮🫕]?\s*)?(.+)$/u', $lines[0], $m)) {
            $this->result['name'] = trim($m[1]);
        }

        $inTags = false;
        $inCategories = false;
        $inBaseRecipe = false;
        $inInstantPot = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') continue;

            // Categories list (multi)
            if (str_starts_with($line, 'categories:')) {
                $inCategories = true;
                continue;
            }
            if ($inCategories && str_starts_with($line, '- ')) {
                $this->result['categories'][] = ucfirst(trim(substr($line, 2)));
                continue;
            }
            if ($inCategories && !str_starts_with($line, '- ')) {
                $inCategories = false;
            }

            // base_recipe list (for derived recipes)
            if (str_starts_with($line, 'base_recipe:')) {
                $inBaseRecipe = true;
                continue;
            }
            if ($inBaseRecipe && str_starts_with($line, '- ')) {
                $this->result['dependencies'][] = trim(substr($line, 2));
                continue;
            }
            if ($inBaseRecipe && !str_starts_with($line, '- ')) {
                $inBaseRecipe = false;
            }

            // Tags list
            if (str_starts_with($line, 'tags:')) {
                $inTags = true;
                continue;
            }
            if ($inTags && str_starts_with($line, '- ')) {
                $this->result['tags'][] = trim(substr($line, 2));
                continue;
            }
            if ($inTags && !str_starts_with($line, '- ')) {
                $inTags = false;
            }

            // Instant pot block
            if (str_starts_with($line, 'instant_pot:')) {
                $inInstantPot = true;
                continue;
            }
            if ($inInstantPot && preg_match('/^\s+(\w+):\s*(.+)$/', $line, $m)) {
                // Store instant pot info
                continue;
            }
            if ($inInstantPot && !str_contains($line, ':')) {
                $inInstantPot = false;
            }

            // Key: value
            if (preg_match('/^(\w[\w_]*):\s*(.+)$/', $line, $m)) {
                $key = $m[1];
                $val = trim($m[2]);

                match ($key) {
                    'slug' => $this->result['slug'] = $val,
                    'recipe_type' => $this->result['recipe_type'] = in_array($val, ['base', 'derived']) ? $val : 'base',
                    'category' => $this->result['categories'] = array_values(array_unique([ucfirst($val)])),
                    'difficulty' => $this->result['difficulty'] = (int) $val,
                    'servings' => $this->result['servings'] = (int) $val,
                    'prep_time' => $this->result['prep_time'] = $this->parseMinutes($val),
                    'cook_time' => $this->result['cook_time'] = $this->parseMinutes($val),
                    'release' => $this->handleReleaseFromHeader($val),
                    'total_time' => $this->result['total_time'] = $this->parseMinutes($val),
                    'cost' => $this->result['cost'] = $val,
                    default => null,
                };
            }
        }

        // Smart inference: detect additional categories from recipe name
        $this->inferCategories();
    }

    // ═══════════════════════════════════════════════════════════
    // Objetivo
    // ═══════════════════════════════════════════════════════════

    private function parseObjective(string $block): void
    {
        $text = $this->stripHeader($block);
        $this->result['objective'] = $text;
        $this->result['description'] = $this->firstSentence($text);
    }

    // ═══════════════════════════════════════════════════════════
    // Ingredientes (YAML-like sub-sections)
    // ═══════════════════════════════════════════════════════════

    private function parseIngredients(string $block): void
    {
        $this->result['ingredients'] = [];

        // Split by sub-headings: ## Proteína, ## Verduras, etc.
        $parts = preg_split('/\n(?=##\s)/u', $block);
        $catMap = [
            'proteína' => 'proteinas', 'proteina' => 'proteinas',
            'verduras' => 'verduras',
            'líquidos' => 'liquidos', 'liquidos' => 'liquidos',
            'condimentos' => 'condimentos',
            'terminación' => 'terminacion', 'terminacion' => 'terminacion',
            'salsa' => 'liquidos',
            'marinado' => 'liquidos',
        ];

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            // Extract category from ## heading
            $catKey = 'condimentos';
            if (preg_match('/^##\s+(.+?)\s*\n/u', $part, $cm)) {
                $catName = strtolower(trim($cm[1]));
                $catKey = $catMap[$catName] ?? $catKey;
                $part = substr($part, strlen($cm[0]));
            }

            // Parse YAML-like ingredients: each starts with "- cantidad:" or "- nombre:"
            $items = $this->parseYamlListItems($part);
            foreach ($items as $item) {
                $name = $item['nombre'] ?? $item['name'] ?? '';
                if (empty($name)) continue;

                $qty = $item['cantidad'] ?? $item['quantity'] ?? null;
                $qty = $qty === 'Al gusto' ? null : ($qty !== null ? $this->parseFraction((string) $qty) : null);

                $this->result['ingredients'][] = [
                    'name' => $name,
                    'category' => $catKey,
                    'quantity' => $qty,
                    'unit' => $item['unidad'] ?? $item['unit'] ?? null,
                    'is_optional' => ($item['opcional'] ?? $item['optional'] ?? false) === true,
                    'notes' => $item['preparación'] ?? $item['preparacion'] ?? $item['notes'] ?? null,
                ];
            }
        }
    }

    /**
     * Parse YAML-like list items (lines starting with "- key: value").
     */
    private function parseYamlListItems(string $text): array
    {
        $items = [];
        $current = [];

        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $trimmed = trim($line);

            // New item starts with "- "
            if (preg_match('/^-\s+(.+)$/', $trimmed, $m)) {
                if (!empty($current)) {
                    $items[] = $current;
                }
                $current = [];
                $rest = $m[1];

                // Parse "key: value" on the same line
                if (preg_match('/^(\w[\wáéíóúüñ]*):\s*(.*)$/u', $rest, $km)) {
                    $current[$km[1]] = $km[2] !== '' ? $km[2] : null;
                }
            }
            // Continuation line or nested property
            elseif (!empty($current) && !empty($trimmed)) {
                if (preg_match('/^(\w[\wáéíóúüñ]*):\s*(.*)$/u', $trimmed, $km)) {
                    $current[$km[1]] = $km[2] !== '' ? $km[2] : null;
                } else {
                    // Append to last key's value
                    $lastKey = array_key_last($current);
                    if ($lastKey) {
                        $current[$lastKey] .= ' ' . $trimmed;
                    }
                }
            }
        }

        if (!empty($current)) {
            $items[] = $current;
        }

        return $items;
    }

    // ═══════════════════════════════════════════════════════════
    // Equipo
    // ═══════════════════════════════════════════════════════════

    private function parseEquipment(string $block): void
    {
        $this->result['equipment'] = [];
        $text = $this->stripHeader($block);

        if (preg_match_all('/^[-*]\s+(.+)$/mu', $text, $items)) {
            foreach ($items[1] as $item) {
                $name = trim($item);
                if (!empty($name)) {
                    $this->result['equipment'][] = $name;
                }
            }
        }

        // Always include Instant Pot if not listed
        $hasIP = false;
        foreach ($this->result['equipment'] as $eq) {
            if (stripos($eq, 'Instant Pot') !== false) $hasIP = true;
        }
        if (!$hasIP) {
            array_unshift($this->result['equipment'], 'Instant Pot');
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Procedimiento
    // ═══════════════════════════════════════════════════════════

    private function parseProcedure(string $block): void
    {
        $this->result['steps'] = [];

        // Split by "## Paso N"
        $parts = preg_split('/\n(?=##\s+Paso\s+\d)/u', $block);
        // Remove header line
        array_shift($parts);

        foreach ($parts as $part) {
            $step = $this->parseStepBlock($part);
            if ($step) {
                $this->result['steps'][] = $step;
            }
        }
    }

    private function parseStepBlock(string $block): ?array
    {
        $step = [
            'action' => '',
            'technical_fundament' => '',
            'what_to_observe' => null,
            'common_errors' => null,
        ];

        // Split by ### sub-sections
        $sections = preg_split('/\n(?=###\s)/u', $block);
        $mainAction = '';

        foreach ($sections as $sec) {
            $sec = trim($sec);
            if (empty($sec)) continue;

            if (preg_match('/^###\s+Acci[óo]n\s*\n(.*)/us', $sec, $sm)) {
                $mainAction = trim($sm[1]);
            } elseif (preg_match('/^###\s+Fundamento\s*t[ée]cnico\s*\n(.*)/us', $sec, $sm)) {
                $step['technical_fundament'] = trim($sm[1]);
            } elseif (preg_match('/^###\s+Qu[ée]\s*observar\s*\n(.*)/us', $sec, $sm)) {
                $step['what_to_observe'] = trim($sm[1]);
            } elseif (preg_match('/^###\s+Error\s*com[úu]n\s*\n(.*)/us', $sec, $sm)) {
                $step['common_errors'] = trim($sm[1]);
            } elseif (preg_match('/^##\s+Paso\s+\d/', $sec)) {
                // Skip the heading itself
            } else {
                // Unrecognized content - add to action
                $clean = preg_replace('/^###+\s*.*?\n/us', '', $sec);
                if (trim($clean)) {
                    $mainAction .= ($mainAction ? "\n" : '') . trim($clean);
                }
            }
        }

        $step['action'] = trim(preg_replace('/\n---+/', '', $mainAction));
        if (empty($step['action'])) return null;
        return $step;
    }

    // ═══════════════════════════════════════════════════════════
    // Resultado esperado
    // ═══════════════════════════════════════════════════════════

    private function parseResultado(string $block): void
    {
        $parts = preg_split('/\n(?=##\s)/u', $block);
        $map = [
            'textura' => 'result_texture',
            'color' => 'result_color',
            'consistencia' => 'result_consistency',
            'salsa' => 'result_consistency',
            'aroma' => 'result_flavor',
            'sabor' => 'result_flavor',
            'temperatura' => 'result_temperature',
            'carne' => 'result_texture',
            'caldo' => 'result_consistency',
            'verduras' => 'result_texture',
            'pollo' => 'result_texture',
            'salsa' => 'result_consistency',
        ];

        foreach ($parts as $part) {
            if (preg_match('/^##\s+(.+?)\s*\n(.*)/us', $part, $m)) {
                $key = strtolower(trim($m[1]));
                $val = trim($m[2]);
                if (!empty($val)) {
                    if (isset($map[$key])) {
                        $field = $map[$key];
                        $this->result[$field] = trim(($this->result[$field] ?? '') . ($this->result[$field] ?? false ? '. ' : '') . $val);
                    }
                    // Siempre guardar en description como resultado incluso si no hay mapeo específico
                    $this->result['result_description'] = trim(($this->result['result_description'] ?? '') . ($this->result['result_description'] ?? false ? '. ' : '') . ucfirst($key) . ': ' . $val);
                }
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Variantes
    // ═══════════════════════════════════════════════════════════

    private function parseVariants(string $block): void
    {
        $this->result['variants'] = [];
        $parts = preg_split('/\n(?=##\s)/u', $block);
        // Skip header
        array_shift($parts);

        foreach ($parts as $part) {
            if (preg_match('/^##\s+(.+?)\s*\n(.*)/us', $part, $m)) {
                $this->result['variants'][] = [
                    'name' => trim($m[1]),
                    'description' => trim($m[2]) ?: null,
                    'ingredients_changes' => null,
                    'procedure_changes' => null,
                ];
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Adaptaciones
    // ═══════════════════════════════════════════════════════════

    private function parseAdaptations(string $block): void
    {
        $this->result['adaptations'] = [];
        $parts = preg_split('/\n(?=##\s)/u', $block);
        array_shift($parts);

        foreach ($parts as $part) {
            if (preg_match('/^##\s+(.+?)\s*\n(.*)/us', $part, $m)) {
                $this->result['adaptations'][] = [
                    'scenario' => trim($m[1]),
                    'text' => trim($m[2]),
                ];
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Conservación
    // ═══════════════════════════════════════════════════════════

    private function parseConservacion(string $block): void
    {
        $parts = preg_split('/\n(?=##\s)/u', $block);

        foreach ($parts as $part) {
            if (preg_match('/^##\s+(.+?)\s*\n(.*)/us', $part, $m)) {
                $key = strtolower(trim($m[1]));
                $val = trim($m[2]);

                match (true) {
                    str_contains($key, 'refriger') => $this->result['storage_refrigeration'] = $val,
                    str_contains($key, 'congel') => $this->result['storage_freezing'] = $val,
                    str_contains($key, 'recalent') => $this->result['storage_reheating'] = $val,
                    default => null,
                };
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Resumen técnico
    // ═══════════════════════════════════════════════════════════

    private function parseResumenTecnico(string $block): void
    {
        $text = $this->stripHeader($block);

        if (preg_match_all('/^[-*]\s*(.+?):\s*(.+)$/mu', $text, $items, PREG_SET_ORDER)) {
            foreach ($items as $item) {
                $key = strtolower(trim($item[1]));
                $val = trim($item[2]);

                match (true) {
                    str_contains($key, 'pressure cook') => $this->result['pressure_cook_time'] = $this->parseMinutes($val),
                    str_contains($key, 'sauté') || str_contains($key, 'saute') => $this->result['saute_time'] = $this->parseMinutes($val),
                    str_contains($key, 'liberación') || str_contains($key, 'liberacion') => $this->handleRelease($val),
                    default => null,
                };
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Conceptos aprendidos
    // ═══════════════════════════════════════════════════════════

    private function parseConcepts(string $block): void
    {
        $this->result['concepts'] = [];
        $text = $this->stripHeader($block);

        if (preg_match_all('/^[-*]\s+(.+)$/mu', $text, $items)) {
            foreach ($items[1] as $item) {
                $this->result['concepts'][] = trim($item);
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Problemas frecuentes
    // ═══════════════════════════════════════════════════════════

    private function parseProblemas(string $block): void
    {
        $this->result['errors'] = [];
        $parts = preg_split('/\n(?=##\s)/u', $block);
        array_shift($parts);

        foreach ($parts as $part) {
            if (!preg_match('/^##\s+(.+?)\s*\n(.*)/us', $part, $m)) continue;

            $problem = trim($m[1]);
            $body = $m[2];

            $cause = null;
            $solution = '';

            if (preg_match('/###\s+Causa\s*\n(.*?)(?=\n###|$)/us', $body, $cm)) {
                $cause = trim($cm[1]);
            }
            if (preg_match('/###\s+Soluci[óo]n\s*\n(.*?)(?=\n###|$)/us', $body, $sm)) {
                $solution = trim($sm[1]);
            }

            if (empty($solution) && !empty(trim($body))) {
                // No explicit ### sections - treat body as solution
                $solution = trim(preg_replace('/^###.*$/mu', '', $body));
            }

            $this->result['errors'][] = [
                'problem' => $problem,
                'possible_cause' => $cause,
                'solution' => $solution,
            ];
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Notas técnicas
    // ═══════════════════════════════════════════════════════════

    private function parsePreparacion(string $block): void
    {
        $this->result['preparation'] = $this->stripHeader($block);
    }

    private function parseChefNotes(string $block): void
    {
        $this->result['chef_notes'] = $this->stripHeader($block);
    }

    // ═══════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════

    private function stripHeader(string $block): string
    {
        // Remove first heading line
        $lines = explode("\n", $block);
        if (!empty($lines) && (str_starts_with(trim($lines[0]), '#') || str_starts_with(trim($lines[0]), 'slug:'))) {
            // Check if first line is a heading or the block starts with metadata
        }
        // Just remove the first ## header
        $text = preg_replace('/^#{1,3}\s+[^\n]+\n/us', '', $block);
        return trim($text);
    }

    private function parseMinutes(string $s): int
    {
        if (preg_match('/(\d+)/', $s, $m)) {
            return (int) $m[1];
        }
        return 0;
    }

    private function parseFraction(string $s): float
    {
        return match ($s) {
            '½' => 0.5, '¼' => 0.25, '¾' => 0.75, '⅓' => 0.33, '⅔' => 0.67,
            'Al gusto' => 0,
            default => (float) str_replace(',', '.', $s) ?: 1,
        };
    }

    private function normalizeRelease(string $s): string
    {
        $s = strtolower($s);
        if (str_contains($s, 'natural')) return 'natural';
        if (str_contains($s, 'rápida') || str_contains($s, 'rapida')) return 'rapida';
        return 'natural';
    }

    /**
     * Infer additional categories from recipe name.
     * E.g., "Caldo de Res" → add "Res" category if not already present.
     */
    private function inferCategories(): void
    {
        $name = $this->result['name'] ?? '';
        if (empty($name)) return;

        $cats = $this->result['categories'] ?? [];
        $nameLower = strtolower($name);

        $keywordMap = [
            'pollo' => 'Pollo', 'pechuga' => 'Pollo', 'muslo' => 'Pollo',
            'res' => 'Res', 'bistec' => 'Res', 'carne de res' => 'Res',
            'cerdo' => 'Cerdo', 'puerco' => 'Cerdo', 'cochinita' => 'Cerdo', 'carnitas' => 'Cerdo',
            'pescado' => 'Pescados', 'salmón' => 'Pescados',
            'camarón' => 'Mariscos', 'camarones' => 'Mariscos', 'calamar' => 'Mariscos',
            'pasta' => 'Pasta', 'spaghetti' => 'Pasta',
            'arroz' => 'Arroz',
            'frijol' => 'Frijoles', 'frijoles' => 'Frijoles',
            'sopa' => 'Sopas', 'caldo' => 'Sopas', 'crema de' => 'Sopas',
            'huevo' => 'Desayunos', 'desayuno' => 'Desayunos',
            'pan' => 'Pan', 'cheesecake' => 'Postres', 'pastel' => 'Postres',
            'verdura' => 'Verduras',
        ];

        $catsLower = array_map('strtolower', $cats);

        foreach ($keywordMap as $keyword => $category) {
            if (stripos($nameLower, $keyword) !== false) {
                if (!in_array(strtolower($category), $catsLower)) {
                    $cats[] = $category;
                    $catsLower[] = strtolower($category);
                }
            }
        }

        $this->result['categories'] = array_values($cats);
    }

    private function handleReleaseFromHeader(string $val): string
    {
        // Extraer tipo y tiempo del formato "Natural 15 min" o "rápida"
        $normalized = $this->normalizeRelease($val);
        $this->result['pressure_release'] = $normalized;
        if ($normalized === 'natural') {
            $this->result['pressure_release_time'] = $this->parseMinutes($val) ?: 10;
        } else {
            $this->result['pressure_release_time'] = 0;
        }
        return $normalized;
    }

    private function handleRelease(string $val): void
    {
        $val = strtolower($val);
        if (str_contains($val, 'natural')) {
            $this->result['pressure_release'] = 'natural';
            $this->result['pressure_release_time'] = $this->parseMinutes($val) ?: 10;
        } elseif (str_contains($val, 'rápida') || str_contains($val, 'rapida')) {
            $this->result['pressure_release'] = 'rapida';
            $this->result['pressure_release_time'] = 0;
        }
    }

    private function firstSentence(string $text): string
    {
        $parts = preg_split('/[.\n]/u', $text, 2);
        return trim($parts[0]) . '.';
    }

    private function categoryIcon(string $name): string
    {
        return match (strtolower($name)) {
            'pollo' => '🐔', 'res' => '🐄', 'cerdo' => '🐖',
            'pescados' => '🐟', 'mariscos' => '🦐', 'pasta' => '🍝',
            'arroz' => '🍚', 'frijoles' => '🫘', 'sopas' => '🍜',
            'desayunos' => '🍳', 'postres' => '🍰', 'pan' => '🍞',
            'verduras' => '🥦', 'salsas' => '🫙', 'bebidas' => '🍵',
            default => '📖',
        };
    }
}
