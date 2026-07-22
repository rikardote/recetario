<?php

namespace App\Services\RmsV2;

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
use Exception;

/**
 * Imports a validated RMS v2.0 document into the database.
 *
 * Only runs if validation passes. Writes all related data in a database transaction.
 */
class RmsV2Importer
{
    /**
     * Import a validated recipe into the database.
     *
     * @throws Exception if import fails
     */
    public function import(array $data, string $sourceMarkdown): Recipe
    {
        // Ensure unique slug
        $slug = $data['slug'];
        $baseSlug = $slug;
        $counter = 1;
        while (Recipe::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        // Resolve categories
        $catName = $data['category'];
        $cat = Category::firstOrCreate(
            ['slug' => Str::slug($catName)],
            ['name' => $catName, 'icon' => $this->categoryIcon($catName)]
        );

        $recipe = Recipe::create([
            'name' => $data['name'],
            'slug' => $slug,
            'recipe_type' => $data['recipe_type'],
            'description' => $data['description'],
            'objective' => $data['objective'],
            'prep_time' => $data['prep_time'],
            'cook_time' => $data['cook_time'],
            'total_time' => $data['total_time'],
            'servings' => $data['servings'],
            'difficulty' => $data['difficulty'],
            'cost' => $data['cost'],
            'result_texture' => $this->extractResultField($data['results'] ?? [], 'Textura'),
            'result_color' => $this->extractResultField($data['results'] ?? [], 'Color'),
            'result_consistency' => $this->extractResultField($data['results'] ?? [], 'Consistencia'),
            'result_flavor' => $this->extractResultField($data['results'] ?? [], 'Sabor'),
            'storage_refrigeration' => $this->extractStorageField($data['storage'] ?? '', 'refriger'),
            'storage_freezing' => $this->extractStorageField($data['storage'] ?? '', 'congel'),
            'storage_reheating' => $this->extractStorageField($data['storage'] ?? '', 'recalent'),
            'pressure_cook_time' => $data['technical']['pressure_cook_time'] ?? null,
            'pressure_release' => $data['pressure_release'] ?? ($data['technical']['pressure_release'] ?? null),
            'pressure_release_time' => $data['technical']['pressure_release_time'] ?? null,
            'saute_time' => $data['technical']['saute_time'] ?? null,
            'chef_notes' => $data['chef_notes'],
            'source_markdown' => $sourceMarkdown,
            'is_published' => true,
        ]);

        // Sync category
        $recipe->categories()->sync([$cat->id => ['is_primary' => true]]);

        // Tags
        if (!empty($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
            $recipe->tags()->sync($tagIds);
        }

        // Equipment
        if (!empty($data['equipment'])) {
            $eqIds = [];
            foreach ($data['equipment'] as $eqName) {
                $eq = Equipment::firstOrCreate(
                    ['slug' => Str::slug($eqName)],
                    ['name' => $eqName, 'description' => $eqName]
                );
                $eqIds[] = $eq->id;
            }
            $recipe->equipment()->sync($eqIds);
        }

        // Ingredients
        foreach ($data['ingredients'] ?? [] as $ing) {
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
                'is_optional' => false,
                'is_recommended' => true,
                'notes' => null,
            ]);
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

        // Variants
        foreach ($data['variants'] ?? [] as $v) {
            RecipeVariant::create([
                'recipe_id' => $recipe->id,
                'name' => $v['title'],
                'description' => $v['content'] ?? null,
            ]);
        }

        // Adaptations
        foreach ($data['adaptations'] ?? [] as $a) {
            RecipeAdaptation::create([
                'recipe_id' => $recipe->id,
                'scenario' => $a['title'],
                'adaptation_text' => $a['content'] ?? '',
            ]);
        }

        // Concepts
        foreach ($data['concepts'] ?? [] as $c) {
            RecipeConcept::create([
                'recipe_id' => $recipe->id,
                'concept_text' => $c,
            ]);
        }

        // Errors
        foreach ($data['errors_list'] ?? [] as $e) {
            RecipeError::create([
                'recipe_id' => $recipe->id,
                'problem' => $e['problem'],
                'possible_cause' => $e['possible_cause'] ?? null,
                'solution' => $e['solution'] ?? '',
            ]);
        }

        return $recipe;
    }

    private function categoryIcon(string $name): string
    {
        return match (mb_strtolower($name)) {
            'pollo' => '🐔', 'res' => '🐄', 'cerdo' => '🐖',
            'pescados' => '🐟', 'mariscos' => '🦐', 'pasta' => '🍝',
            'arroz' => '🍚', 'frijoles' => '🫘', 'sopas' => '🍜',
            'desayunos' => '🍳', 'postres' => '🍰', 'pan' => '🍞',
            'verduras' => '🥦', 'salsas' => '🫙', 'bebidas' => '🍵',
            default => '📖',
        };
    }

    private function extractResultField(array $results, string $fieldName): ?string
    {
        foreach ($results as $r) {
            if (mb_strtolower($r['title']) === mb_strtolower($fieldName)) {
                return $r['content'];
            }
        }
        return null;
    }

    private function extractStorageField(string $storage, string $keyword): ?string
    {
        $lines = explode("\n", $storage);
        foreach ($lines as $line) {
            $line = trim($line);
            if (mb_stripos($line, $keyword) !== false) {
                return $line;
            }
        }
        return null;
    }
}
