<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Recipe;
use App\Models\Favorite;

#[Layout('layouts.app', ['title' => 'Favoritos'])]
new class extends Component
{
    public function render()
    {
        $favorites = Recipe::where('is_published', true)
            ->whereHas('favorites')
            ->with('categories')
            ->get();

        return view('components.⚡favorite-button', [
            'favorites' => $favorites,
        ]);
    }
};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">⭐ Favoritos</h1>

    @if($favorites->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $recipe)
                <a href="/recetas/{{ $recipe->slug }}"
                   class="block bg-white border border-gray-100 rounded-2xl p-6 hover:border-orange-200 hover:shadow-lg transition-all">
                    <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                        {{ $recipe->category->icon }} {{ $recipe->category->name }}
                    </span>
                    <h3 class="font-semibold text-gray-900 mt-2 mb-2">{{ $recipe->name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $recipe->description }}</p>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 text-gray-400">
            <p>No tienes recetas favoritas</p>
        </div>
    @endif
</div>
