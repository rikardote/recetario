<?php

use Livewire\Component;
use App\Models\Recipe;

new class extends Component
{
    public $selected = [];

    public function addRecipe($id)
    {
        if (!in_array($id, $this->selected)) $this->selected[] = $id;
    }

    public function removeRecipe($id)
    {
        $this->selected = array_values(array_filter($this->selected, fn($s) => $s != $id));
    }
};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Lista de Compras</h1>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="font-semibold text-gray-900 mb-4">Recetas</h2>
            @foreach(Recipe::where('is_published', true)->whereNotIn('id', $this->selected)->orderBy('name')->get() as $r)
                <button wire:click="addRecipe({{ $r->id }})" class="w-full text-left flex items-center justify-between bg-gray-50 hover:bg-orange-50 rounded-xl p-3 mb-2">
                    <span class="text-sm">{{ $r->category->icon }} {{ $r->name }}</span>
                    <span class="text-xs text-green-500">+ Agregar</span>
                </button>
            @endforeach
            @php $selectedRecipes = Recipe::whereIn('id', $this->selected)->with('recipeIngredients.ingredient')->get(); @endphp
            @if($selectedRecipes->count())
                <h3 class="font-semibold text-gray-900 mt-6 mb-3">Seleccionadas</h3>
                @foreach($selectedRecipes as $r)
                    <div class="flex items-center justify-between bg-orange-50 rounded-xl p-3 mb-2">
                        <span class="text-sm">{{ $r->name }}</span>
                        <button wire:click="removeRecipe({{ $r->id }})" class="text-red-400 hover:text-red-600">✕</button>
                    </div>
                @endforeach
            @endif
        </div>
        <div>
            <h2 class="font-semibold text-gray-900 mb-4">Ingredientes</h2>
            @if($selectedRecipes->count())
                @php
                    $all = collect();
                    foreach ($selectedRecipes as $r) {
                        foreach ($r->recipeIngredients as $ri) {
                            $key = $ri->ingredient->name . '|' . $ri->unit;
                            if (isset($all[$key])) {
                                $all[$key]['quantity'] += $ri->quantity ?? 0;
                            } else {
                                $all[$key] = ['name' => $ri->ingredient->name, 'unit' => $ri->unit, 'quantity' => $ri->quantity ?? 1, 'category' => $ri->category];
                            }
                        }
                    }
                @endphp
                @foreach($all->groupBy('category') as $cat => $items)
                    <div class="mb-4">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-2">{{ $cat }}</h3>
                        @foreach($items as $item)
                            <div class="flex items-center gap-2 py-1.5 border-b border-gray-50 text-sm">
                                <input type="checkbox" class="rounded border-gray-300 text-orange-500">
                                <span><strong>{{ $item['quantity'] }}</strong> {{ $item['unit'] }} {{ $item['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <p class="text-gray-400 text-sm">Selecciona recetas</p>
            @endif
        </div>
    </div>
</div>
