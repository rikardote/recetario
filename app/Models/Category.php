<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function publishedRecipes(): HasMany
    {
        return $this->hasMany(Recipe::class)->where('is_published', true);
    }
}
