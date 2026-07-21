<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('objective');
            $table->integer('prep_time')->comment('minutes');
            $table->integer('cook_time')->comment('minutes');
            $table->integer('total_time')->comment('minutes');
            $table->integer('servings');
            $table->tinyInteger('difficulty')->comment('1-5 stars');
            $table->string('cost')->nullable();

            // Resultado esperado
            $table->text('result_texture')->nullable();
            $table->text('result_color')->nullable();
            $table->text('result_consistency')->nullable();
            $table->text('result_temperature')->nullable();
            $table->text('result_flavor')->nullable();

            // Conservación
            $table->text('storage_refrigeration')->nullable();
            $table->text('storage_freezing')->nullable();
            $table->text('storage_reheating')->nullable();

            // Resumen técnico
            $table->integer('pressure_cook_time')->nullable()->comment('minutes');
            $table->string('pressure_release')->nullable()->comment('natural, quick, combination');
            $table->integer('pressure_release_time')->nullable()->comment('minutes');
            $table->integer('saute_time')->nullable()->comment('minutes');

            // Notas del chef
            $table->text('chef_notes')->nullable();

            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
