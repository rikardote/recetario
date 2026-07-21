<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Fácil', 'Rápido', 'Una olla', 'Pot in Pot',
            'Congelado', 'Meal Prep', 'Económico', 'Sin Gluten',
            'Keto', 'Picante', 'Alto en proteína', 'Vegetariano',
            'Vegano', 'Bajo en carbohidratos', 'Sin lactosa',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag,
                'slug' => \Str::slug($tag),
            ]);
        }
    }
}
