<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recipes — indexes for filters and ordering
        Schema::table('recipes', function (Blueprint $table) {
            $table->index('is_published');
            $table->index('difficulty');
            $table->index('recipe_type');
            $table->index('slug');
            $table->index(['is_published', 'created_at']);
        });

        // Recipe steps — index for ordering
        Schema::table('recipe_steps', function (Blueprint $table) {
            $table->index(['recipe_id', 'step_number']);
        });

        // Recipe ingredients — index for category grouping
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->index(['recipe_id', 'category']);
        });

        // Pivot tables
        Schema::table('category_recipe', function (Blueprint $table) {
            $table->index(['category_id', 'recipe_id']);
        });

        Schema::table('recipe_tags', function (Blueprint $table) {
            $table->index(['recipe_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['is_published']);
            $table->dropIndex(['difficulty']);
            $table->dropIndex(['recipe_type']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_published', 'created_at']);
        });

        Schema::table('recipe_steps', function (Blueprint $table) {
            $table->dropIndex(['recipe_id', 'step_number']);
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->dropIndex(['recipe_id', 'category']);
        });

        Schema::table('category_recipe', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'recipe_id']);
        });

        Schema::table('recipe_tags', function (Blueprint $table) {
            $table->dropIndex(['recipe_id', 'tag_id']);
        });
    }
};
