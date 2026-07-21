<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Recipe;

#[Layout('layouts.app', ['title' => 'Planeador Semanal'])]
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
        $selectedRecipes = Recipe::whereIn('id', $this->selected)->with('category')->get();
        $available = Recipe::where('is_published', true)
            ->whereNotIn('id', $this->selected)
            ->orderBy('name')
            ->get();

        return view('components.⚡weekly-planner', [
            'selectedRecipes' => $selectedRecipes,
            'available' => $available,
        ]);
    }
};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Planeador Semanal</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="font-semibold text-gray-900 mb-4">Mi menú ({{ count($selected) }})</h2>
            <div class="space-y-3">
                @forelse($selectedRecipes as $recipe)
                    <div class="flex items-center justify-between bg-white border border-gray-100 rounded-xl p-4">
                        <div>
                            <span class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded-full">{{ $recipe->category->name }}</span>
                            <span class="ml-2 font-medium text-gray-900">{{ $recipe->name }}</span>
                        </div>
                        <button wire:click="removeRecipe({{ $recipe->id }})" class="text-red-400 hover:text-red-600 text-sm">✕</button>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">Selecciona recetas del listado</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-4">Recetas disponibles</h2>
            <div class="space-y-2">
                @foreach($available as $recipe)
                    <button wire:click="addRecipe({{ $recipe->id }})"
                        class="w-full text-left flex items-center justify-between bg-gray-50 hover:bg-orange-50 rounded-xl p-3 transition-colors">
                        <span class="text-sm text-gray-700">{{ $recipe->category->icon }} {{ $recipe->name }}</span>
                        <span class="text-xs text-gray-400">{{ str_repeat('⭐', $recipe->difficulty) }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
