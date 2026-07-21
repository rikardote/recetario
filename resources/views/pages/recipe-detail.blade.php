<?php

use Livewire\Component;
use App\Models\Recipe;
use App\Models\RecipeIngredient;

new class extends Component
{
    public $recipe;

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe->load([
            'categories', 'steps', 'equipment', 'tags',
            'variants', 'adaptations', 'concepts', 'errors',
            'recipeIngredients.ingredient', 'images', 'videos',
        ]);
    }
};
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <a href="/" class="hover:text-gray-600">Inicio</a>
        <span>/</span>
        <a href="/recetas" class="hover:text-gray-600">Recetas</a>
        <span>/</span>
        <a href="/recetas?category={{ $recipe->category->slug }}" class="hover:text-gray-600">{{ $recipe->category->name }}</a>
        <span>/</span>
        <span class="text-gray-600">{{ $recipe->name }}</span>
    </nav>

    {{-- Header --}}
    <div class="mb-10">
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="text-xs font-medium text-orange-600 bg-orange-50 px-3 py-1 rounded-full">
                {{ $recipe->category->icon }} {{ $recipe->category->name }}
            </span>
            @foreach($recipe->categories as $cat)
                @if($cat->id !== ($recipe->category?->id))
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                        {{ $cat->icon }} {{ $cat->name }}
                    </span>
                @endif
            @endforeach
            <span class="text-sm text-gray-400">{{ str_repeat('⭐', $recipe->difficulty) }}</span>
            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">{{ $recipe->cost }}</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $recipe->name }}</h1>
        <p class="text-lg text-gray-500 max-w-2xl leading-relaxed">{{ $recipe->description }}</p>

        <div class="flex flex-wrap gap-6 mt-6 text-sm text-gray-500">
            <div><span class="text-gray-400">⏱ Prep:</span> <span class="font-medium text-gray-700">{{ $recipe->prep_time }} min</span></div>
            <div><span class="text-gray-400">🔥 Cocción:</span> <span class="font-medium text-gray-700">{{ $recipe->cook_time }} min</span></div>
            <div><span class="text-gray-400">🕐 Total:</span> <span class="font-medium text-gray-700">{{ $recipe->total_time }} min</span></div>
            <div><span class="text-gray-400">🍽 Porciones:</span> <span class="font-medium text-gray-700">{{ $recipe->servings }}</span></div>
        </div>

        @if($recipe->tags->count())
            <div class="flex flex-wrap gap-2 mt-4">
                @foreach($recipe->tags as $tag)
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        {{-- Action buttons --}}
        <div class="flex flex-wrap gap-3 mt-5">
            <a href="/recetas/{{ $recipe->slug }}/lista-compras"
               class="inline-flex items-center gap-2 bg-orange-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-orange-600 transition-colors">
                🧾 Lista de compras
            </a>
        </div>
    </div>

    {{-- Objetivo --}}
    <div class="bg-orange-50 border border-orange-100 rounded-2xl p-6 mb-8">
        <h2 class="text-sm font-semibold text-orange-700 uppercase tracking-wide mb-2">🎯 Objetivo</h2>
        <p class="text-gray-700 leading-relaxed">{{ $recipe->objective }}</p>
    </div>

    {{-- Procedimiento --}}
    <h2 class="text-xl font-bold text-gray-900 mb-6">📋 Procedimiento</h2>
    <div class="space-y-8 mb-12">
        @foreach($recipe->steps as $step)
            <div class="border border-gray-100 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <span class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                        {{ $step->step_number }}
                    </span>
                    <div class="flex-1 space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Paso {{ $step->step_number }}</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $step->action }}</p>
                        </div>
                        @if($step->technical_fundament)
                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                <h4 class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1">🔬 Fundamento técnico</h4>
                                <p class="text-sm text-blue-800 leading-relaxed">{{ $step->technical_fundament }}</p>
                            </div>
                        @endif
                        @if($step->what_to_observe)
                            <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                                <h4 class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">👁️ Qué observar</h4>
                                <p class="text-sm text-green-800 leading-relaxed">{{ $step->what_to_observe }}</p>
                            </div>
                        @endif
                        @if($step->common_errors)
                            <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                                <h4 class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-1">❌ Errores comunes</h4>
                                <p class="text-sm text-red-800 leading-relaxed">{{ $step->common_errors }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Ingredientes --}}
    <h2 class="text-xl font-bold text-gray-900 mb-6">🧂 Ingredientes</h2>
    <div class="space-y-6 mb-12">
        @php $grouped = $recipe->recipeIngredients->groupBy('category'); @endphp
        @foreach(['proteinas' => '🍖 Proteínas', 'verduras' => '🥕 Verduras', 'liquidos' => '💧 Líquidos', 'condimentos' => '🌿 Condimentos', 'terminacion' => '✨ Terminación'] as $cat => $label)
            @if(isset($grouped[$cat]) && $grouped[$cat]->count())
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">{{ $label }}</h3>
                    <ul class="space-y-2">
                        @foreach($grouped[$cat] as $ri)
                            <li class="flex items-center gap-2 text-gray-700">
                                <span class="text-gray-400">•</span>
                                <span>
                                    @if($ri->quantity) <strong>{{ $ri->quantity }}</strong> {{ $ri->unit }} de @endif
                                    {{ $ri->ingredient->name }}
                                    @if($ri->is_recommended) <span class="text-xs text-orange-500 font-medium">(recomendado)</span> @endif
                                    @if($ri->is_optional) <span class="text-xs text-gray-400">(opcional)</span> @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Equipo --}}
    @if($recipe->equipment->count())
        <h2 class="text-xl font-bold text-gray-900 mb-6">🔧 Equipo necesario</h2>
        <div class="space-y-4 mb-12">
            @foreach($recipe->equipment as $eq)
                <div class="border border-gray-100 rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $eq->name }}</h3>
                    <p class="text-sm text-gray-500 mb-3">{{ $eq->description }}</p>
                    @if($eq->when_to_use)
                        <div class="text-sm text-green-700 bg-green-50 rounded-lg p-3 mb-2"><strong>✅ Usar cuando:</strong> {{ $eq->when_to_use }}</div>
                    @endif
                    @if($eq->when_not_to_use)
                        <div class="text-sm text-red-700 bg-red-50 rounded-lg p-3"><strong>❌ No usar cuando:</strong> {{ $eq->when_not_to_use }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Variantes --}}
    @if($recipe->variants->count())
        <h2 class="text-xl font-bold text-gray-900 mb-6">🔄 Variantes</h2>
        <div class="space-y-4 mb-12">
            @foreach($recipe->variants as $variant)
                <div class="border border-gray-100 rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $variant->name }}</h3>
                    @if($variant->description)<p class="text-sm text-gray-500 mb-3">{{ $variant->description }}</p>@endif
                    @if($variant->ingredients_changes)<div class="bg-yellow-50 rounded-lg p-3 mb-2 text-sm"><strong>🧂 Ingredientes:</strong> {{ $variant->ingredients_changes }}</div>@endif
                    @if($variant->procedure_changes)<div class="bg-yellow-50 rounded-lg p-3 text-sm"><strong>📋 Procedimiento:</strong> {{ $variant->procedure_changes }}</div>@endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Adaptaciones --}}
    @if($recipe->adaptations->count())
        <h2 class="text-xl font-bold text-gray-900 mb-6">🔀 Adaptaciones</h2>
        <div class="space-y-4 mb-12">
            @foreach($recipe->adaptations as $adaptation)
                <div class="border border-gray-100 rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $adaptation->scenario }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $adaptation->adaptation_text }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Conceptos aprendidos --}}
    @if($recipe->concepts->count())
        <h2 class="text-xl font-bold text-gray-900 mb-6">📚 Conceptos aprendidos</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-12">
            @foreach($recipe->concepts as $concept)
                <div class="flex items-center gap-3 bg-green-50 border border-green-100 rounded-xl p-4">
                    <span class="text-green-500 text-xl">✓</span>
                    <span class="font-medium text-green-800">{{ $concept->concept_text }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Solución de problemas --}}
    @if($recipe->errors->count())
        <h2 class="text-xl font-bold text-gray-900 mb-6">⚠️ Solución de problemas</h2>
        <div class="space-y-4 mb-12">
            @foreach($recipe->errors as $error)
                <div class="border border-red-100 rounded-2xl p-6 bg-red-50/50">
                    <h3 class="font-semibold text-red-800 mb-3">⚠️ {{ $error->problem }}</h3>
                    @if($error->possible_cause)<div class="text-sm text-gray-600 mb-2"><strong class="text-red-700">Posible causa:</strong> {{ $error->possible_cause }}</div>@endif
                    <div class="text-sm text-gray-600"><strong class="text-green-700">✅ Solución:</strong> {{ $error->solution }}</div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Resultado esperado --}}
    <h2 class="text-xl font-bold text-gray-900 mb-6">✅ Resultado esperado</h2>
    <div class="space-y-4 mb-12">
        @foreach(['result_texture' => 'Textura','result_color' => 'Color','result_consistency' => 'Consistencia','result_flavor' => 'Sabor esperado'] as $field => $label)
            @if($recipe->$field)
                <div class="border border-gray-100 rounded-2xl p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">{{ $label }}</h3>
                    <p class="text-gray-700">{{ $recipe->$field }}</p>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Resumen técnico --}}
    <h2 class="text-xl font-bold text-gray-900 mb-6">⚡ Resumen Técnico</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-12">
        @if($recipe->pressure_cook_time)
            <div class="bg-gray-50 rounded-2xl p-6"><h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Pressure Cook</h3><p class="text-2xl font-bold text-gray-900">{{ $recipe->pressure_cook_time }} <span class="text-sm font-normal text-gray-400">min</span></p></div>
        @endif
        @if($recipe->pressure_release)
            <div class="bg-gray-50 rounded-2xl p-6"><h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Liberación</h3><p class="text-xl font-bold text-gray-900 capitalize">{{ $recipe->pressure_release }}@if($recipe->pressure_release_time) <span class="text-sm font-normal text-gray-400">· {{ $recipe->pressure_release_time }} min</span>@endif</p></div>
        @endif
        @if($recipe->saute_time)
            <div class="bg-gray-50 rounded-2xl p-6"><h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Sauté</h3><p class="text-2xl font-bold text-gray-900">{{ $recipe->saute_time }} <span class="text-sm font-normal text-gray-400">min</span></p></div>
        @endif
    </div>

    {{-- Conservación --}}
    <h2 class="text-xl font-bold text-gray-900 mb-6">❄️ Conservación</h2>
    <div class="space-y-4 mb-12">
        @foreach(['storage_refrigeration' => '🧊 Refrigeración','storage_freezing' => '❄️ Congelación','storage_reheating' => '🔥 Recalentado'] as $field => $label)
            @if($recipe->$field)
                <div class="border border-gray-100 rounded-2xl p-6"><h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">{{ $label }}</h3><p class="text-gray-700">{{ $recipe->$field }}</p></div>
            @endif
        @endforeach
    </div>

    {{-- Notas del chef --}}
    @if($recipe->chef_notes)
        <div class="mt-12 border-t pt-8">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">👨‍🍳 Notas del chef</h3>
            <div class="bg-gray-50 rounded-2xl p-6 text-gray-700 leading-relaxed">{{ $recipe->chef_notes }}</div>
        </div>
    @endif
</div>
