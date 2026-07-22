<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\RecipeList;

// Home
Route::livewire('/', 'pages::home-page')->name('home');

// Recipe listing
Route::livewire('/recetas', RecipeList::class)->name('recipes.index');

// Recipe detail
Route::livewire('/recetas/{recipe:slug}', 'pages::recipe-detail')->name('recipes.show');

// Search
Route::livewire('/buscar', 'pages::recipe-search')->name('search');

// Techniques
Route::livewire('/tecnicas', 'pages::technique-list')->name('techniques.index');

// Concepts
Route::livewire('/conceptos', 'pages::concept-list')->name('concepts.index');

// Functions
Route::livewire('/funciones', 'pages::function-list')->name('functions.index');

// Equipment
Route::livewire('/accesorios', 'pages::equipment-list')->name('equipment.index');

// Compare
Route::livewire('/comparar', 'pages::compare-recipes')->name('compare');

// Weekly Planner
Route::livewire('/planeador', 'pages::weekly-planner')->name('planner');

// Shopping List
Route::livewire('/lista-compras', 'pages::shopping-list')->name('shopping-list');

// Printable Shopping List per recipe
Route::livewire('/recetas/{recipe:slug}/lista-compras', 'pages::shopping-list-print')->name('recipes.shopping-list');

// Recipe Importer (RMS v2.0)
Route::livewire('/importar', 'pages::recipe-importer-v2')->name('recipes.import');

// Favorites
Route::livewire('/favoritos', 'pages::favorite-button')->name('favorites');

// DEBUG: quitar después
Route::get('/debug', function() {
    try {
        $r = \App\Models\Recipe::with('categories', 'steps', 'tags')->first();
        if (!$r) return 'No recipes in DB';
        $out = "Name: {$r->name}\nSlug: {$r->slug}\n";
        $out .= "Categories: " . $r->categories->pluck('name')->implode(', ') . "\n";
        $out .= "Steps: " . $r->steps->count() . "\n";
        $out .= "Category accessor: " . ($r->category?->name ?? 'NULL') . "\n";
        return response($out, 200)->header('Content-Type', 'text/plain');
    } catch(\Exception $e) {
        return response($e->getMessage() . "\n\n" . $e->getTraceAsString(), 500)
            ->header('Content-Type', 'text/plain');
    }
});
