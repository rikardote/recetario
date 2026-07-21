<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add recipe_type to recipes
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('recipe_type')->default('base')->after('slug')->comment('base, derived');
        });

        // 2. Create recipe_dependencies pivot
        Schema::create('recipe_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete()->comment('Derived recipe');
            $table->foreignId('dependency_recipe_id')->constrained('recipes')->cascadeOnDelete()->comment('Base recipe');
            $table->decimal('quantity', 10, 3)->nullable();
            $table->string('quantity_unit')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unique(['recipe_id', 'dependency_recipe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_dependencies');

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('recipe_type');
        });
    }
};
