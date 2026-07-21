<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Recipe;

#[Layout('layouts.app', ['title' => 'Búsqueda'])]
new class extends Component
{
    public $q = '';
    public $results = [];

    public function mount()
    {
        $this->q = request('q', '');
        $this->search();
    }

    public function updatedQ()
    {
        $this->search();
    }

    public function search()
    {
        if (strlen($this->q) >= 2) {
            $this->results = Recipe::with('categories')
                ->where('is_published', true)
                ->where(function($q) {
                    $q->where('name', 'like', "%{$this->q}%")
                      ->orWhere('description', 'like', "%{$this->q}%")
                      ->orWhere('objective', 'like', "%{$this->q}%")
                      ->orWhereHas('categories', fn($c) => $c->where('name', 'like', "%{$this->q}%"))
                      ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%{$this->q}%"));
                })
                ->latest()
                ->take(20)
                ->get();
        } else {
            $this->results = [];
        }
    }

    public function render()
    {
        return view('components.⚡recipe-search');
    }
};
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Búsqueda</h1>

    <div class="mb-8">
        <input type="text" wire:model.live.debounce.300ms="q" placeholder="Buscar por nombre, ingrediente, técnica, categoría..."
            class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-2xl focus:border-orange-400 focus:ring-4 focus:ring-orange-100 outline-none transition-all">
    </div>

    @if($q && count($results) > 0)
        <p class="text-sm text-gray-400 mb-6">{{ count($results) }} resultados para "{{ $q }}"</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach($results as $recipe)
                <a href="/recetas/{{ $recipe->slug }}"
                   class="block bg-white border border-gray-100 rounded-2xl p-6 hover:border-orange-200 hover:shadow-lg transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                            {{ $recipe->category->icon }} {{ $recipe->category->name }}
                        </span>
                        <span class="text-xs text-gray-400">{{ str_repeat('⭐', $recipe->difficulty) }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $recipe->name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $recipe->description }}</p>
                    <div class="text-xs text-gray-400">⏱ {{ $recipe->total_time }} min · 🍽 {{ $recipe->servings }} porc.</div>
                </a>
            @endforeach
        </div>
    @elseif($q && count($results) === 0)
        <div class="text-center py-16 text-gray-400">
            <p class="text-lg mb-2">Sin resultados para "{{ $q }}"</p>
            <p class="text-sm">Intenta con otros términos</p>
        </div>
    @endif
</div>
