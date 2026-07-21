<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create pivot table
        Schema::create('category_recipe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->unique(['category_id', 'recipe_id']);
        });

        // 2. Migrate existing category_id to pivot
        DB::statement('
            INSERT INTO category_recipe (category_id, recipe_id, is_primary)
            SELECT category_id, id, true FROM recipes WHERE category_id IS NOT NULL
        ');

        // 3. Drop the old column
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE recipes SET category_id = (
                SELECT category_id FROM category_recipe
                WHERE recipe_id = recipes.id AND is_primary = true
                LIMIT 1
            )
        ');

        Schema::dropIfExists('category_recipe');
    }
};
