<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'objective',
        'prep_time', 'cook_time', 'total_time', 'servings', 'difficulty', 'cost',
        'result_texture', 'result_color', 'result_consistency',
        'result_temperature', 'result_flavor',
        'storage_refrigeration', 'storage_freezing', 'storage_reheating',
        'pressure_cook_time', 'pressure_release', 'pressure_release_time',
        'saute_time', 'chef_notes', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'difficulty' => 'integer',
            'prep_time' => 'integer',
            'cook_time' => 'integer',
            'total_time' => 'integer',
            'servings' => 'integer',
        ];
    }

    protected $appends = ['category'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_recipe')
            ->withPivot('is_primary')
            ->orderByPivot('is_primary', 'desc');
    }

    public function getCategoryAttribute(): ?Category
    {
        // Prefer the primary category, fallback to first
        return $this->categories->first(fn($c) => $c->pivot->is_primary)
            ?? $this->categories->first();
    }

    /**
     * Sync categories for a recipe (used by parser).
     */
    public function syncCategories(array $categoryIds, int $primaryId): void
    {
        $data = [];
        foreach ($categoryIds as $id) {
            $data[$id] = ['is_primary' => $id === $primaryId];
        }
        $this->categories()->sync($data);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_number');
    }

    public function images(): HasMany
    {
        return $this->hasMany(RecipeImage::class)->orderBy('order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(RecipeVideo::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(RecipeVariant::class);
    }

    public function adaptations(): HasMany
    {
        return $this->hasMany(RecipeAdaptation::class);
    }

    public function concepts(): HasMany
    {
        return $this->hasMany(RecipeConcept::class);
    }

    public function errors(): HasMany
    {
        return $this->hasMany(RecipeError::class);
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'recipe_equipment');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(RecipeView::class);
    }

    public function difficultyStars(): string
    {
        return str_repeat('⭐', $this->difficulty);
    }
}
