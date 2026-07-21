<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    protected $fillable = [
        'recipe_id', 'ingredient_id', 'category',
        'quantity', 'unit', 'is_recommended', 'is_optional', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_recommended' => 'boolean',
            'is_optional' => 'boolean',
            'quantity' => 'float',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
