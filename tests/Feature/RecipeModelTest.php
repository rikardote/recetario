<?php

use App\Models\Category;
use App\Models\Recipe;
use App\Models\Tag;

beforeEach(function () {
    $this->pollo = Category::factory()->create(['name' => 'Pollo', 'slug' => 'pollo', 'icon' => '🐔']);
});

test('recipe belongs to multiple categories', function () {
    $res = Category::factory()->create(['name' => 'Res', 'slug' => 'res', 'icon' => '🐄']);
    $recipe = Recipe::factory()->create();

    $recipe->categories()->sync([
        $this->pollo->id => ['is_primary' => true],
        $res->id => ['is_primary' => false],
    ]);

    $recipe->refresh();

    expect($recipe->categories)->toHaveCount(2);
    expect($recipe->category->id)->toBe($this->pollo->id);
    expect($recipe->category->name)->toBe('Pollo');
});

test('category has published recipes', function () {
    $published = Recipe::factory()->count(2)->create();
    foreach ($published as $r) {
        $r->categories()->sync([$this->pollo->id => ['is_primary' => true]]);
    }

    $unpublished = Recipe::factory()->create(['is_published' => false]);
    $unpublished->categories()->sync([$this->pollo->id => ['is_primary' => true]]);

    expect($this->pollo->publishedRecipes()->count())->toBe(2);
});

test('recipe has steps in order', function () {
    $recipe = Recipe::factory()->create();
    $recipe->categories()->sync([$this->pollo->id => ['is_primary' => true]]);

    $recipe->steps()->createMany([
        ['step_number' => 2, 'action' => 'Segundo paso', 'technical_fundament' => 'Ciencia'],
        ['step_number' => 1, 'action' => 'Primer paso', 'technical_fundament' => 'Física'],
    ]);

    $recipe->refresh();

    expect($recipe->steps->pluck('step_number')->toArray())->toBe([1, 2]);
    expect($recipe->steps->first()->action)->toBe('Primer paso');
});

test('recipe has tags', function () {
    $recipe = Recipe::factory()->create();
    $tag = Tag::factory()->create(['name' => 'Fácil', 'slug' => 'facil']);

    $recipe->tags()->attach($tag);

    expect($recipe->tags)->toHaveCount(1);
    expect($recipe->tags->first()->name)->toBe('Fácil');
});

test('recipe categories are ordered by primary first', function () {
    $res = Category::factory()->create(['name' => 'Res', 'slug' => 'res', 'icon' => '🐄']);
    $recipe = Recipe::factory()->create();

    // Res is primary, Pollo is secondary
    $recipe->categories()->sync([
        $res->id => ['is_primary' => true],
        $this->pollo->id => ['is_primary' => false],
    ]);

    $recipe->refresh();

    expect($recipe->category->id)->toBe($res->id);
    expect($recipe->categories->first()->id)->toBe($res->id);
});

test('recipe can be base or derived', function () {
    $base = Recipe::factory()->base()->create();
    $derived = Recipe::factory()->derived()->create();

    expect($base->isBase())->toBeTrue();
    expect($base->isDerived())->toBeFalse();
    expect($derived->isDerived())->toBeTrue();
    expect($derived->isBase())->toBeFalse();
});

test('recipe difficulty stars helper', function () {
    $recipe = Recipe::factory()->create(['difficulty' => 3]);

    expect($recipe->difficultyStars())->toBe('⭐⭐⭐');
});
