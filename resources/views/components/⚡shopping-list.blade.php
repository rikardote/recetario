<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Recipe;

#[Layout('layouts.app', ['title' => 'Lista de Compras'])]
new class extends Component
{
    public $selected = [];

    public function addRecipe($id)
    {
        if (!in_array($id, $this->selected)) {
            $this->selected[] = $id;
        }
    }

    public function removeRecipe($id)
    {
        $this->selected = array_values(array_filter($this->selected, fn($s) => $s != $id));
    }

    public function render()
    {
        $selectedRecipes = Recipe::whereIn('id', $this->selected)
            ->with('recipeIngredients.ingredient')
            ->get();

        $allIngredients = collect();
        foreach ($selectedRecipes as $recipe) {
            foreach ($recipe->recipeIngredients as $ri) {
                $key = $ri->ingredient->name . '|' . $ri->unit;
                if (isset($allIngredients[$key])) {
                    $allIngredients[$key]['quantity'] += $ri->quantity ?? 0;
                    $allIngredients[$key]['recipes'][] = $recipe->name;
                } else {
                    $allIngredients[$key] = [
                        'name' => $ri->ingredient->name,
                        'unit' => $ri->unit,
                        'quantity' => $ri->quantity ?? 1,
                        'category' => $ri->category,
                        'recipes' => [$recipe->name],
                    ];
                }
            }
        }

        $available = Recipe::where('is_published', true)
            ->whereNotIn('id', $this->selected)
            ->orderBy('name')->get();

        return view('components.⚡shopping-list', [
            'allIngredients' => $allIngredients->groupBy('category'),
            'selectedRecipes' => $selectedRecipes,
            'available' => $available,
        ]);
    }
};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Lista de Compras</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="font-semibold text-gray-900 mb-4">Seleccionar recetas</h2>
            <div class="space-y-2">
                @foreach($available as $recipe)
                    <button wire:click="addRecipe({{ $recipe->id }})"
                        class="w-full text-left flex items-center justify-between bg-gray-50 hover:bg-orange-50 rounded-xl p-3 transition-colors">
                        <span class="text-sm text-gray-700">{{ $recipe->category->icon }} {{ $recipe->name }}</span>
                        <span class="text-xs text-green-500">+ Agregar</span>
                    </button>
                @endforeach
            </div>

            @if($selectedRecipes->count())
                <h3 class="font-semibold text-gray-900 mt-6 mb-3">Seleccionadas</h3>
                @foreach($selectedRecipes as $recipe)
                    <div class="flex items-center justify-between bg-orange-50 rounded-xl p-3 mb-2">
                        <span class="text-sm text-gray-700">{{ $recipe->name }}</span>
                        <button wire:click="removeRecipe({{ $recipe->id }})" class="text-red-400 hover:text-red-600 text-sm">✕</button>
                    </div>
                @endforeach
            @endif
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-4">Ingredientes</h2>
            @if($allIngredients->count())
                @foreach($allIngredients as $category => $items)
                    <div class="mb-4">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-2">{{ $category }}</h3>
                        @foreach($items as $item)
                            <div class="flex items-center gap-2 py-1.5 border-b border-gray-50 text-sm">
                                <input type="checkbox" class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                                <span class="text-gray-700">
                                    <strong>{{ $item['quantity'] }}</strong> {{ $item['unit'] }} de {{ $item['name'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <p class="text-gray-400 text-sm">Selecciona recetas para generar la lista</p>
            @endif
        </div>
    </div>
</div>
