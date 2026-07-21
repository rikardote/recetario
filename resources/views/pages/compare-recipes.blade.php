<?php

use Livewire\Component;
use App\Models\Recipe;

new class extends Component
{
    public $recipe1_id = null;
    public $recipe2_id = null;

    public function getRecipe1Property()
    {
        return $this->recipe1_id ? Recipe::with('steps', 'categories', 'tags')->find($this->recipe1_id) : null;
    }

    public function getRecipe2Property()
    {
        return $this->recipe2_id ? Recipe::with('steps', 'categories', 'tags')->find($this->recipe2_id) : null;
    }
};
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Comparar Recetas</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Receta 1</label>
            <select wire:model.live="recipe1_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl outline-none">
                <option value="">Seleccionar...</option>
                @foreach(Recipe::where('is_published', true)->orderBy('name')->get() as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Receta 2</label>
            <select wire:model.live="recipe2_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl outline-none">
                <option value="">Seleccionar...</option>
                @foreach(Recipe::where('is_published', true)->orderBy('name')->get() as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($this->recipe1 && $this->recipe2)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach([$this->recipe1, $this->recipe2] as $recipe)
                <div class="border border-gray-100 rounded-2xl p-6">
                    <span class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded-full">{{ $recipe->category->icon }} {{ $recipe->category->name }}</span>
                    <h3 class="font-semibold text-gray-900 mt-2 mb-4">{{ $recipe->name }}</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-400">Dificultad</span><span>{{ str_repeat('⭐', $recipe->difficulty) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Tiempo</span><span>{{ $recipe->total_time }} min</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Porciones</span><span>{{ $recipe->servings }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Costo</span><span>{{ $recipe->cost }}</span></div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 text-gray-400"><p>Selecciona dos recetas</p></div>
    @endif
</div>
