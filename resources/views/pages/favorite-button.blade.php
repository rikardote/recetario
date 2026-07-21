<?php

use Livewire\Component;
use App\Models\Recipe;

new class extends Component {};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">⭐ Favoritos</h1>
    @php $favorites = Recipe::where('is_published', true)->whereHas('favorites')->with('categories')->get(); @endphp
    @if($favorites->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $r)
                <a href="/recetas/{{ $r->slug }}" class="block bg-white border border-gray-100 rounded-2xl p-6 hover:border-orange-200 hover:shadow-lg transition-all">
                    <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">{{ $r->category->icon }} {{ $r->category->name }}</span>
                    <h3 class="font-semibold text-gray-900 mt-2 mb-2">{{ $r->name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $r->description }}</p>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 text-gray-400"><p>No tienes favoritos aún</p></div>
    @endif
</div>
