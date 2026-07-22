<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
 <div class="flex items-center justify-between mb-6">
 <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Recetas</h1>
 {{-- Mobile filter toggle --}}
 <button x-data @click="$refs.sidebar.classList.toggle('hidden')"
 class="lg:hidden flex items-center gap-2 text-sm font-medium text-orange-600 bg-orange-50 px-4 py-2 rounded-xl hover:bg-orange-100 transition-colors">
 <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
 Filtros
 </button>
 </div>

 {{-- Mobile search (always visible) --}}
 <div class="lg:hidden mb-4">
 <input type="text" wire:model.live="search" placeholder="Buscar recetas..."
 class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:border-orange-400 dark:focus:border-orange-500 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-orange-100 outline-none text-sm">
 </div>

 <div class="flex flex-col lg:flex-row gap-8">
 {{-- Grid — primero en móvil --}}
 <div class="flex-1 lg:order-2">
 <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
 @forelse($recipes as $recipe)
 <a href="/recetas/{{ $recipe->slug }}"
 class="group block dark:bg-gray-800 dark:border-gray-700 bg-white border border-gray-100 rounded-2xl p-6 hover:border-orange-200 dark:hover:border-orange-700 hover:shadow-lg transition-all">
 <div class="flex items-start justify-between mb-3">
 <div class="flex items-center gap-1.5 flex-wrap">
 <span class="text-xs font-medium {{ $recipe->isDerived() ?'>bg-blue-50 text-blue-700' :'>bg-green-50 text-green-700' }} px-2 py-0.5 rounded-full">
 {{ $recipe->recipeTypeIcon() }}
 </span>
 <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
 {{ $recipe->category->icon }} {{ $recipe->category->name }}
 </span>
 </div>
 <span class="text-xs shrink-0">{{ str_repeat('⭐', $recipe->difficulty) }}</span>
 </div>
 <h3 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-orange-700 dark:group-hover:text-orange-400 mb-2">{{ $recipe->name }}</h3>
 <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 line-clamp-2 mb-4">{{ $recipe->description }}</p>
 <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
 <span>⏱ {{ $recipe->total_time }} min</span>
 <span>🍽 {{ $recipe->servings }} porc.</span>
 <span>{{ $recipe->cost }}</span>
 </div>
 </a>
 @empty
 <div class="col-span-full text-center py-16 text-gray-400 dark:text-gray-500">
 <p class="text-lg">No se encontraron recetas</p>
 <button wire:click="$set('category','>')" class="mt-2 text-sm text-orange-600 hover:underline">Limpiar filtros</button>
 </div>
 @endforelse
 </div>

 <div class="mt-8">
 {{ $recipes->links() }}
 </div>
 </div>

 {{-- Sidebar — oculto en móvil, aparece con toggle --}}
 <aside x-ref="sidebar" class="hidden lg:block lg:w-64 flex-shrink-0 lg:order-1">
 <div class="space-y-6">
 {{-- Desktop search --}}
 <div class="hidden lg:block">
 <input type="text" wire:model.live="search" placeholder="Buscar..."
 class="w-full px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-xl focus:border-orange-400 dark:focus:border-orange-500 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-orange-100 outline-none text-sm">
 </div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 dark:text-gray-100 mb-2">Dificultad</h3>
 <div class="space-y-1">
 @foreach(range(1,5) as $d)
 <button wire:click="$set('difficulty','>{{ $difficulty == $d ?'>' : $d }}')"
 class="block w-full text-left px-3 py-1.5 text-sm rounded-lg transition-colors {{ $difficulty == (string)$d ?'>bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :'>text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
 {{ str_repeat('⭐', $d) }}
 </button>
 @endforeach
 </div>
 </div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 dark:text-gray-100 mb-2">Categorías</h3>
 @foreach(\App\Models\Category::withCount('publishedRecipes')->get() as $cat)
 <button wire:click="filterCategory('{{ $cat->slug }}')"
 class="flex justify-between w-full text-left px-3 py-1.5 text-sm rounded-lg transition-colors {{ $category === $cat->slug ?'>bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :'>text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
 <span>{{ $cat->icon }} {{ $cat->name }}</span>
 <span class="text-gray-400 dark:text-gray-500 text-xs">{{ $cat->published_recipes_count }}</span>
 </button>
 @endforeach
 </div>
 </div>
 </aside>
 </div>
</div>
