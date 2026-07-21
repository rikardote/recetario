<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;

new class extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $difficulty = '';
    public $tag = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filterCategory($slug)
    {
        $this->category = $this->category === $slug ? '' : $slug;
        $this->resetPage();
    }

    public function recipes()
    {
        return Recipe::with('category', 'tags')
            ->where('is_published', true)
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            }))
            ->when($this->category, fn($q) => $q->whereHas('category', fn($c) => $c->where('slug', $this->category)))
            ->when($this->difficulty, fn($q) => $q->where('difficulty', $this->difficulty))
            ->when($this->tag, fn($q) => $q->whereHas('tags', fn($t) => $t->where('slug', $this->tag)))
            ->latest()
            ->paginate(12);
    }
};
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Recetas</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="space-y-6">
                <div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..."
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-orange-400 focus:ring-2 focus:ring-orange-100 outline-none text-sm">
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Dificultad</h3>
                    <div class="space-y-1">
                        @foreach(range(1,5) as $d)
                            <button wire:click="$set('difficulty', {{ $difficulty == $d ? '' : $d }})"
                                class="block w-full text-left px-3 py-1.5 text-sm rounded-lg {{ $difficulty == $d ? 'bg-orange-100 text-orange-800' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ str_repeat('⭐', $d) }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Categorías</h3>
                    <div class="space-y-1">
                        @php $cats = Category::withCount('publishedRecipes')->get(); @endphp
                        @foreach($cats as $cat)
                            <button wire:click="filterCategory('{{ $cat->slug }}')"
                                class="flex justify-between w-full text-left px-3 py-1.5 text-sm rounded-lg {{ $category === $cat->slug ? 'bg-orange-100 text-orange-800' : 'text-gray-600 hover:bg-gray-50' }}">
                                <span>{{ $cat->icon }} {{ $cat->name }}</span>
                                <span class="text-gray-400 text-xs">{{ $cat->published_recipes_count }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        <!-- Grid -->
        <div class="flex-1">
            @php $recipes = $this->recipes(); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($recipes as $recipe)
                    <a href="/recetas/{{ $recipe->slug }}"
                       class="group block bg-white border border-gray-100 rounded-2xl p-6 hover:border-orange-200 hover:shadow-lg transition-all">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                                {{ $recipe->category->icon }} {{ $recipe->category->name }}
                            </span>
                            <span class="text-xs">{{ str_repeat('⭐', $recipe->difficulty) }}</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-orange-700 mb-2">{{ $recipe->name }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $recipe->description }}</p>
                        <div class="flex items-center gap-4 text-xs text-gray-400">
                            <span>⏱ {{ $recipe->total_time }} min</span>
                            <span>🍽 {{ $recipe->servings }} porc.</span>
                            <span>{{ $recipe->cost }}</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-16 text-gray-400">
                        <p class="text-lg">No se encontraron recetas</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-8">
                {{ $recipes->links() }}
            </div>
        </div>
    </div>
</div>
