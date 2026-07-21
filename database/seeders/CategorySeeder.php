<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pollo', 'slug' => 'pollo', 'icon' => '🐔', 'description' => 'Recetas con pollo preparadas en Instant Pot.'],
            ['name' => 'Res', 'slug' => 'res', 'icon' => '🐄', 'description' => 'Cortes de res cocinados a presión.'],
            ['name' => 'Cerdo', 'slug' => 'cerdo', 'icon' => '🐖', 'description' => 'Recetas con carne de cerdo.'],
            ['name' => 'Pescados', 'slug' => 'pescados', 'icon' => '🐟', 'description' => 'Pescados y filetes al vapor o presión.'],
            ['name' => 'Mariscos', 'slug' => 'mariscos', 'icon' => '🦐', 'description' => 'Camarones, calamares y más.'],
            ['name' => 'Pasta', 'slug' => 'pasta', 'icon' => '🍝', 'description' => 'Pastas rápidas en una sola olla.'],
            ['name' => 'Arroz', 'slug' => 'arroz', 'icon' => '🍚', 'description' => 'Arroces perfectos con técnica de presión.'],
            ['name' => 'Frijoles', 'slug' => 'frijoles', 'icon' => '🫘', 'description' => 'Frijoles desde cero sin remojo.'],
            ['name' => 'Sopas', 'slug' => 'sopas', 'icon' => '🍜', 'description' => 'Sopas y caldos con sabor profundo.'],
            ['name' => 'Desayunos', 'slug' => 'desayunos', 'icon' => '🍳', 'description' => 'Desayunos rápidos y nutritivos.'],
            ['name' => 'Postres', 'slug' => 'postres', 'icon' => '🍰', 'description' => 'Postres al vapor y presión.'],
            ['name' => 'Pan', 'slug' => 'pan', 'icon' => '🍞', 'description' => 'Panes al vapor.'],
            ['name' => 'Verduras', 'slug' => 'verduras', 'icon' => '🥦', 'description' => 'Verduras al vapor perfectas.'],
            ['name' => 'Salsas', 'slug' => 'salsas', 'icon' => '🫙', 'description' => 'Salsas y aderezos.'],
            ['name' => 'Bebidas', 'slug' => 'bebidas', 'icon' => '🍵', 'description' => 'Bebidas calientes y frías.'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
