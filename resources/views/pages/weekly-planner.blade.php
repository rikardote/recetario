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
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Planeador Semanal</h1>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="font-semibold text-gray-900 mb-4">Mi menú ({{ count($selected) }})</h2>
            @php $selectedRecipes = Recipe::whereIn('id', $this->selected)->with('category')->get(); @endphp
            @forelse($selectedRecipes as $recipe)
                <div class="flex items-center justify-between bg-white border border-gray-100 rounded-xl p-4 mb-2">
                    <div><span class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded-full">{{ $recipe->category->name }}</span><span class="ml-2 font-medium">{{ $recipe->name }}</span></div>
                    <button wire:click="removeRecipe({{ $recipe->id }})" class="text-red-400 hover:text-red-600">✕</button>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Selecciona recetas</p>
            @endforelse
        </div>
        <div>
            <h2 class="font-semibold text-gray-900 mb-4">Disponibles</h2>
            @foreach(Recipe::where('is_published', true)->whereNotIn('id', $this->selected)->orderBy('name')->get() as $r)
                <button wire:click="addRecipe({{ $r->id }})" class="w-full text-left flex items-center justify-between bg-gray-50 hover:bg-orange-50 rounded-xl p-3 mb-2">
                    <span class="text-sm">{{ $r->category->icon }} {{ $r->name }}</span>
                    <span class="text-xs text-green-500">+ Agregar</span>
                </button>
            @endforeach
        </div>
    </div>
</div>
