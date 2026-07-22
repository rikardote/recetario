<?php

use Livewire\Component;
use App\Models\Recipe;
use App\Models\Technique;

new class extends Component {};
?>

<div>
    <!-- Hero -->
    <section class="py-20 md:py-32 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 tracking-tight leading-tight mb-6">
                Aprende a cocinar con <span class="text-orange-500">Instant Pot</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                El manual más completo en español. Domina técnicas culinarias, comprende el porqué de cada paso y crea tus propias recetas con criterio técnico.
            </p>
            <div class="max-w-xl mx-auto mb-8">
                <form action="/buscar" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Buscar recetas, técnicas, ingredientes..."
                        class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-2xl focus:border-orange-400 focus:ring-4 focus:ring-orange-100 outline-none transition-all">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-orange-500 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-orange-600 transition-colors">
                        Buscar
                    </button>
                </form>
            </div>
            <div class="flex justify-center gap-8 text-sm text-gray-400">
                <span>{{ Recipe::where('is_published', true)->count() }} recetas</span>
                <span>·</span>
                <span>{{ Technique::count() }} técnicas</span>
                <span>·</span>
                <span>5 niveles</span>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Categorías</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
            @php $cats = Category::withCount('publishedRecipes')->get(); @endphp
            @foreach($cats as $cat)
                <a href="/recetas?category={{ $cat->slug }}"
                   class="group flex flex-col items-center p-4 bg-gray-50 rounded-2xl hover:bg-orange-50 hover:border-orange-200 border border-transparent transition-all">
                    <span class="text-3xl mb-2">{{ $cat->icon }}</span>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-orange-700">{{ $cat->name }}</span>
                    <span class="text-xs text-gray-400 mt-1">{{ $cat->published_recipes_count }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Recent Recipes -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Recetas recientes</h2>
            <a href="/recetas" class="text-sm font-medium text-orange-600 hover:text-orange-700">Ver todas →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php $recent = Recipe::with('categories')->where('is_published', true)->latest()->take(6)->get(); @endphp
            @foreach($recent as $recipe)
                <a href="/recetas/{{ $recipe->slug }}"
                   class="group block bg-white border border-gray-100 rounded-2xl p-6 hover:border-orange-200 hover:shadow-lg hover:shadow-orange-50 transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                            {{ $recipe->category->icon ?? '' }} {{ $recipe->category->name }}
                        </span>
                        <span class="text-xs text-gray-400">{{ str_repeat('⭐', $recipe->difficulty) }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-orange-700 mb-2">{{ $recipe->name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $recipe->description }}</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span>⏱ {{ $recipe->total_time }} min</span>
                        <span>🍽 {{ $recipe->servings }} porc.</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Learning levels -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Ruta de aprendizaje</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @php $levels = [['n'=>1,'t'=>'Principiante','d'=>'Funciones básicas','s'=>'⭐'],['n'=>2,'t'=>'Básico','d'=>'Sellado y primeras recetas','s'=>'⭐⭐'],['n'=>3,'t'=>'Intermedio','d'=>'Salsas y reducciones','s'=>'⭐⭐⭐'],['n'=>4,'t'=>'Avanzado','d'=>'Pot in Pot y capas','s'=>'⭐⭐⭐⭐'],['n'=>5,'t'=>'Experto','d'=>'Recetas complejas','s'=>'⭐⭐⭐⭐⭐']]; @endphp
            @foreach($levels as $l)
                <a href="/recetas?difficulty={{ $l['n'] }}"
                   class="block bg-white border border-gray-100 rounded-2xl p-5 hover:border-orange-200 hover:shadow-md transition-all">
                    <div class="text-lg mb-2">{{ $l['s'] }}</div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $l['t'] }}</h3>
                    <p class="text-xs text-gray-500">{{ $l['d'] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <!-- CTA -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-3xl p-8 md:p-12 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Listo para dominar tu Instant Pot?</h2>
            <p class="text-gray-600 max-w-xl mx-auto mb-8">Explora recetas y descubre los fundamentos técnicos detrás de cada preparación.</p>
            <div class="flex justify-center gap-4">
                <a href="/recetas" class="bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition-colors">Explorar recetas</a>
                <a href="/tecnicas" class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold border border-gray-200 hover:border-orange-300 transition-colors">Ver técnicas</a>
            </div>
        </div>
    </section>
</div>
