<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'storage_info', 'usage_info'];

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function substitutions(): HasMany
    {
        return $this->hasMany(IngredientSubstitution::class);
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')
            ->withPivot(['quantity', 'unit', 'category', 'is_recommended', 'is_optional', 'notes']);
    }
}
