<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeVariant extends Model
{
    protected $fillable = ['recipe_id', 'name', 'description', 'ingredients_changes', 'procedure_changes'];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
