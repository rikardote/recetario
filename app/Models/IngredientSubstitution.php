<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientSubstitution extends Model
{
    protected $fillable = ['ingredient_id', 'substitute_ingredient_id', 'notes'];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function substitute(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'substitute_ingredient_id');
    }
}
