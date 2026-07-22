<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'category_recipe')
            ->withPivot('is_primary');
    }

    public function publishedRecipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'category_recipe')
            ->withPivot('is_primary')
            ->where('is_published', true);
    }
}
