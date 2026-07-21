<?php

use Livewire\Component;
use App\Models\Recipe;

new class extends Component
{
    public $q = '';
    public $results = [];

    public function mount()
    {
        $this->q = request('q', '');
        if ($this->q) $this->search();
    }

    public function updatedQ()
    {
        $this->search();
    }

    public function search()
    {
        if (strlen($this->q) >= 2) {
            $this->results = Recipe::with('category')
                ->where('is_published', true)
                ->where(function($q) {
                    $q->where('name', 'like', "%{$this->q}%")
                      ->orWhere('description', 'like', "%{$this->q}%")
                      ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$this->q}%"));
                })
                ->latest()->take(20)->get();
        } else {
            $this->results = [];
        }
    }
};
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Búsqueda</h1>
    <div class="mb-8">
        <input type="text" wire:model.live.debounce.300ms="q" placeholder="Buscar recetas..."
            class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-2xl focus:border-orange-400 focus:ring-4 focus:ring-orange-100 outline-none">
    </div>
    @if($q && count($results) > 0)
        <p class="text-sm text-gray-400 mb-6">{{ count($results) }} resultados</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach($results as $recipe)
                <a href="/recetas/{{ $recipe->slug }}" class="block bg-white border border-gray-100 rounded-2xl p-6 hover:border-orange-200 hover:shadow-lg transition-all">
                    <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">{{ $recipe->category->icon }} {{ $recipe->category->name }}</span>
                    <h3 class="font-semibold text-gray-900 mt-2 mb-2">{{ $recipe->name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $recipe->description }}</p>
                </a>
            @endforeach
        </div>
    @elseif($q)
        <div class="text-center py-16 text-gray-400"><p>Sin resultados</p></div>
    @endif
</div>
