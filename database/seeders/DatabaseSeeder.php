<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            EquipmentSeeder::class,
            TechniqueSeeder::class,
            IngredientSeeder::class,
            InstantPotFunctionSeeder::class,
            ConceptSeeder::class,
            RecipeSeeder::class,
            BaseRecipesSeeder::class,
        ]);
    }
}
