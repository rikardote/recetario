<?php

use Livewire\Component;
use App\Services\RecipeMarkdownParser;

new class extends Component
{
    public $markdown = '';
    public $parsed = null;
    public $warnings = [];
    public $imported = false;
    public $importedRecipe = null;

    public function preview()
    {
        $this->imported = false;
        $this->importedRecipe = null;

        if (empty(trim($this->markdown))) {
            $this->warnings = ['El texto está vacío.'];
            $this->parsed = null;
            return;
        }

        $parser = new RecipeMarkdownParser($this->markdown);
        $data = $parser->parse();
        $this->parsed = $data['result'];
        $this->warnings = $data['warnings'];
    }

    public function import()
    {
        if (empty(trim($this->markdown))) return;

        $parser = new RecipeMarkdownParser($this->markdown);
        $recipe = $parser->import();

        $this->imported = true;
        $this->importedRecipe = $recipe;
    }

    public function render()
    {
        return view('components.⚡recipe-importer');
    }
};
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">📥 Importar Receta</h1>
    <p class="text-gray-500 mb-8">Pega tu receta en formato Markdown y el sistema la procesará automáticamente.</p>

    @if($imported && $importedRecipe)
        {{-- Success --}}
        <div class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center mb-8">
            <div class="text-5xl mb-4">✅</div>
            <h2 class="text-2xl font-bold text-green-800 mb-2">¡Receta importada!</h2>
            <p class="text-green-700 mb-6">{{ $importedRecipe->name }}</p>
            <div class="flex justify-center gap-4">
                <a href="/recetas/{{ $importedRecipe->slug }}"
                   class="bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                    Ver receta →
                </a>
                <button wire:click="$set('imported', false)"
                   class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold border border-gray-200 hover:bg-gray-50 transition-colors">
                    Importar otra
                </button>
            </div>
        </div>
    @endif

    {{-- Input + Preview layout --}}
    <div class="grid grid-cols-1 {{ $parsed ? 'lg:grid-cols-2' : '' }} gap-8">
        {{-- Input --}}
        <div>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700 text-sm">📝 Markdown</h2>
                    <button wire:click="preview" class="bg-orange-500 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">
                        🔍 Previsualizar
                    </button>
                </div>
                <textarea
                    wire:model="markdown"
                    rows="25"
                    placeholder="Pega aquí tu receta en formato Markdown...

Ejemplo:
# 🍗 Nombre de la Receta

## Información General
| Campo | Valor |
| Categoría | Pollo |
| Dificultad | ⭐⭐☆☆☆ |
..."
                    class="w-full px-5 py-4 text-sm font-mono border-0 resize-none focus:ring-0 focus:outline-none"
                ></textarea>
            </div>

            {{-- Format help --}}
            <details class="mt-4 group">
                <summary class="text-sm text-gray-400 cursor-pointer hover:text-gray-500">📋 Ver formato aceptado (v1.0)</summary>
                <div class="mt-3 bg-gray-50 rounded-xl p-4 text-xs font-mono text-gray-500 whitespace-pre-wrap overflow-auto max-h-96">
---
version: 1.0
language: es-MX
---

# 🍗 Choripollo

slug: choripollo
recipe_type: base
category: pollo
difficulty: 2
servings: 4
prep_time: 10 min
cook_time: 6 min
release: rápida
total_time: 25 min
cost: $$

tags:
- pollo
- queso
---

# Objetivo
Preparar un pollo jugoso con chorizo...

---

# Ingredientes

## Proteína
- cantidad: 1
  unidad: kg
  nombre: Pechuga de pollo
  preparación: Cubos de 4 cm

## Verduras
- cantidad: 1
  unidad: pieza
  nombre: Cebolla

## Condimentos
- cantidad: Al gusto
  nombre: Sal

---

# Equipo
- Instant Pot
- Tabla
- Cuchillo

---

# Procedimiento

## Paso 1
### Acción
Dorar el chorizo.
### Fundamento técnico
El chorizo libera grasa.
### Qué observar
Color ligeramente oscuro.
### Error común
Sobre cocinar.

## Paso 2
### Acción
Agregar cebolla.
### Fundamento técnico
Desarrolla dulzor.

---

# Resultado esperado
## Textura
Pollo muy jugoso.
## Color
Rojo intenso.

---

# Variantes
## Con crema
Agregar 100 ml de crema.

---

# Adaptaciones
## Pollo congelado
10 minutos extra.

---

# Conservación
## Refrigerador
4 días.
## Congelador
3 meses.

---

# Resumen técnico
- Sauté: 8 min
- Pressure Cook: 6 min
- Liberación: Rápida

---

# Conceptos aprendidos
- Sellado
- Calor residual

---

# Problemas frecuentes
## El queso quedó duro
### Causa
Agregado antes de cocinar.
### Solución
Agregarlo al finalizar.

---

# Notas técnicas
El queso nunca debe cocinarse bajo presión.
                </div>
            </details>
        </div>

        {{-- Preview --}}
        @if($parsed)
            <div>
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden sticky top-20">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-700 text-sm">👁️ Previsualización</h2>
                        <span class="text-xs text-gray-400">{{ count($parsed['steps'] ?? []) }} pasos · {{ count($parsed['ingredients'] ?? []) }} ingredientes</span>
                    </div>

                    <div class="px-5 py-4 space-y-4 max-h-[600px] overflow-y-auto text-sm">
                        {{-- Name --}}
                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wide">Nombre</span>
                            <p class="font-bold text-gray-900 text-lg">{{ $parsed['name'] ?? '—' }}</p>
                        </div>

                        {{-- Info --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <span class="text-xs text-gray-400">Categorías</span>
                                <p class="font-medium text-gray-700 text-sm">{{ implode(', ', $parsed['categories'] ?? [$parsed['category'] ?? '—']) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <span class="text-xs text-gray-400">Tipo</span>
                                <p class="font-medium text-sm {{ ($parsed['recipe_type'] ?? 'base') === 'derived' ? 'text-blue-700' : 'text-green-700' }}">
                                    {{ ($parsed['recipe_type'] ?? 'base') === 'derived' ? '🍽️ Derivada' : '🧱 Base' }}
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <span class="text-xs text-gray-400">Dificultad</span>
                                <p class="font-medium text-gray-700 text-sm">{{ str_repeat('⭐', $parsed['difficulty'] ?? 1) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <span class="text-xs text-gray-400">Porciones</span>
                                <p class="font-medium text-gray-700 text-sm">{{ $parsed['servings'] ?? '—' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <span class="text-xs text-gray-400">Prep</span>
                                <p class="font-medium text-gray-700 text-sm">{{ $parsed['prep_time'] ?? 0 }} min</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <span class="text-xs text-gray-400">Cocción</span>
                                <p class="font-medium text-gray-700 text-sm">{{ $parsed['cook_time'] ?? 0 }} min</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <span class="text-xs text-gray-400">Total</span>
                                <p class="font-medium text-gray-700 text-sm">{{ $parsed['total_time'] ?? 0 }} min</p>
                            </div>
                        </div>

                        @if(!empty($parsed['pressure_release']))
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs text-gray-400">Resumen técnico</span>
                                <p class="font-medium text-gray-700 text-sm">
                                    @if(!empty($parsed['pressure_cook_time'])) Pressure Cook: {{ $parsed['pressure_cook_time'] }} min · @endif
                                    @if(!empty($parsed['saute_time'])) Sauté: {{ $parsed['saute_time'] }} min · @endif
                                    Liberación: {{ $parsed['pressure_release'] }}
                                    @if(!empty($parsed['pressure_release_time'])) {{ $parsed['pressure_release_time'] }} min @endif
                                </p>
                            </div>
                        @endif

                        {{-- Objective --}}
                        @if(!empty($parsed['objective']))
                            <div>
                                <span class="text-xs text-gray-400 uppercase tracking-wide">Objetivo</span>
                                <p class="text-gray-600">{{ Str::limit($parsed['objective'], 200) }}</p>
                            </div>
                        @endif

                        {{-- Ingredients count --}}
                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wide">Ingredientes ({{ count($parsed['ingredients'] ?? []) }})</span>
                            <ul class="mt-1 space-y-1">
                                @foreach(array_slice($parsed['ingredients'] ?? [], 0, 8) as $ing)
                                    <li class="text-gray-600 flex items-center gap-1">
                                        <span class="text-gray-300">•</span>
                                        @if($ing['quantity']) <strong>{{ $ing['quantity'] }}</strong> {{ $ing['unit'] }} @endif
                                        {{ $ing['name'] }}
                                        @if($ing['is_optional']) <span class="text-xs text-gray-400">(opcional)</span> @endif
                                    </li>
                                @endforeach
                                @if(count($parsed['ingredients'] ?? []) > 8)
                                    <li class="text-gray-400 text-xs">... y {{ count($parsed['ingredients']) - 8 }} más</li>
                                @endif
                            </ul>
                        </div>

                        {{-- Steps count --}}
                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wide">Pasos ({{ count($parsed['steps'] ?? []) }})</span>
                            @foreach(array_slice($parsed['steps'] ?? [], 0, 3) as $i => $step)
                                <div class="mt-1 flex gap-2">
                                    <span class="text-orange-500 font-bold text-xs flex-shrink-0">{{ $i + 1 }}.</span>
                                    <span class="text-gray-600">{{ Str::limit($step['action'], 100) }}</span>
                                </div>
                            @endforeach
                            @if(count($parsed['steps'] ?? []) > 3)
                                <p class="text-xs text-gray-400 mt-1">... y {{ count($parsed['steps']) - 3 }} pasos más</p>
                            @endif
                        </div>

                        {{-- Warnings --}}
                        @if(!empty($warnings))
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3">
                                <span class="text-xs font-semibold text-yellow-700">⚠️ Advertencias</span>
                                <ul class="mt-1 space-y-1">
                                    @foreach($warnings as $w)
                                        <li class="text-xs text-yellow-700">{{ $w }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{-- Import button --}}
                    <div class="px-5 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                        <button wire:click="import"
                            class="w-full bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                            ✅ Importar receta
                        </button>
                        <p class="text-xs text-gray-400 text-center mt-2">Se crearán ingredientes y equipo nuevos si no existen</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
