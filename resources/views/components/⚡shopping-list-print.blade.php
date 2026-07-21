<?php

use Livewire\Component;
use App\Models\Recipe;

new class extends Component
{
    public $recipe;

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe->load(['recipeIngredients.ingredient']);
    }
};
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 print:py-2 print:px-2">
    {{-- No-print header --}}
    <div class="print:hidden mb-8">
        <a href="/recetas/{{ $recipe->slug }}" class="text-sm text-orange-600 hover:text-orange-700 mb-4 inline-block">← Volver a la receta</a>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">🧾 Lista de Compras</h1>
            <button onclick="window.print()" class="bg-orange-500 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-orange-600 transition-colors">
                🖨️ Imprimir
            </button>
        </div>
        <p class="text-gray-500 mt-2">{{ $recipe->name }}</p>
        <p class="text-sm text-gray-400">{{ $recipe->servings }} porciones · ⏱ {{ $recipe->total_time }} min</p>
    </div>

    {{-- Print-only header --}}
    <div class="hidden print:block mb-6">
        <h1 class="text-xl font-bold text-black">🧾 Lista de Compras</h1>
        <p class="text-sm text-gray-600">{{ $recipe->name }} — {{ $recipe->servings }} porciones</p>
        <hr class="mt-2 border-gray-300">
    </div>

    {{-- Ingredients grouped by store section --}}
    @php
        $storeSections = [
            'proteinas' => ['title' => '🥩 Carnicería', 'emoji' => '🥩'],
            'verduras' => ['title' => '🥕 Frutas y Verduras', 'emoji' => '🥕'],
            'liquidos' => ['title' => '🧴 Despensa (Líquidos)', 'emoji' => '🧴'],
            'condimentos' => ['title' => '🌿 Especias y Condimentos', 'emoji' => '🌿'],
            'terminacion' => ['title' => '🧀 Lácteos y Quesos', 'emoji' => '🧀'],
        ];

        $grouped = $recipe->recipeIngredients->groupBy('category');
    @endphp

    <div class="space-y-6">
        @foreach($storeSections as $catKey => $section)
            @php $items = $grouped->get($catKey, collect()); @endphp
            @if($items->isNotEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden print:border-gray-400 print:rounded-none print:shadow-none">
                    {{-- Section header --}}
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 print:bg-gray-100 print:border-gray-400">
                        <h2 class="font-semibold text-gray-800 text-sm">{{ $section['title'] }}</h2>
                    </div>

                    {{-- Items --}}
                    <div class="divide-y divide-gray-100 print:divide-gray-300">
                        @foreach($items as $ri)
                            <div class="flex items-center px-5 py-3">
                                {{-- Checkbox (hidden in print but space preserved) --}}
                                <div class="flex-shrink-0 mr-3 print:hidden">
                                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-400 cursor-pointer">
                                </div>
                                <div class="flex-shrink-0 mr-3 hidden print:block">
                                    <span class="inline-block w-5 h-5 border border-black rounded text-center text-xs leading-5">☐</span>
                                </div>

                                {{-- Ingredient info --}}
                                <div class="flex-1 min-w-0">
                                    <span class="text-gray-900 font-medium">
                                        @if($ri->quantity)
                                            <strong class="text-base">{{ $ri->quantity }}</strong> {{ $ri->unit }}
                                        @endif
                                        {{ $ri->ingredient->name }}
                                    </span>
                                    @if($ri->is_optional)
                                        <span class="text-xs text-gray-400 ml-1">(opcional)</span>
                                    @endif
                                </div>

                                {{-- Badges --}}
                                <div class="flex-shrink-0 flex items-center gap-2 ml-3">
                                    @if($ri->is_recommended)
                                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full print:bg-gray-200 print:text-gray-700">★ recomendado</span>
                                    @endif
                                    @if($ri->notes)
                                        <span class="text-xs text-gray-400 hidden sm:inline print:text-gray-500">{{ $ri->notes }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Summary --}}
    <div class="mt-8 border-t border-gray-200 pt-6 print:border-black">
        <div class="flex justify-between text-sm">
            <div>
                <p class="text-gray-400 print:text-gray-600">Total de ingredientes: <strong class="text-gray-700">{{ $recipe->recipeIngredients->count() }}</strong></p>
                <p class="text-gray-400 print:text-gray-600">Opcionales: <strong class="text-gray-700">{{ $recipe->recipeIngredients->where('is_optional', true)->count() }}</strong></p>
            </div>
            <div class="text-right text-gray-400 print:text-gray-600">
                <p>{{ $recipe->name }}</p>
                <p>{{ $recipe->servings }} porciones</p>
                <p>⏱ {{ $recipe->total_time }} min total</p>
            </div>
        </div>
        <p class="text-xs text-gray-300 mt-4 print:hidden">Marca los ingredientes que ya tienes antes de ir al súper</p>
    </div>

    {{-- Print button (bottom, no-print) --}}
    <div class="mt-8 text-center print:hidden">
        <button onclick="window.print()" class="bg-orange-500 text-white px-8 py-3 rounded-xl text-base font-semibold hover:bg-orange-600 transition-colors shadow-lg shadow-orange-200">
            🖨️ Imprimir lista de compras
        </button>
    </div>
</div>
