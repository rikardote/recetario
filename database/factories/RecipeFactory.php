<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'recipe_type' => 'base',
            'description' => $this->faker->sentence(),
            'objective' => $this->faker->paragraph(),
            'prep_time' => $this->faker->numberBetween(5, 30),
            'cook_time' => $this->faker->numberBetween(5, 60),
            'total_time' => fn(array $attrs) => $attrs['prep_time'] + $attrs['cook_time'] + 10,
            'servings' => $this->faker->numberBetween(2, 8),
            'difficulty' => $this->faker->numberBetween(1, 5),
            'cost' => $this->faker->randomElement(['$', '$$', '$$$']),
            'is_published' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn() => ['is_published' => true]);
    }

    public function base(): static
    {
        return $this->state(fn() => ['recipe_type' => 'base']);
    }

    public function derived(): static
    {
        return $this->state(fn() => ['recipe_type' => 'derived']);
    }
}
