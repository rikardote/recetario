<?php

use Livewire\Component;
use App\Services\RmsV2\RmsV2Parser;
use App\Services\RmsV2\RmsV2Importer;

new class extends Component
{
    public $markdown = '';
    public $parsedData = null;
    public $validationErrors = [];
    public $validSections = [];
    public $imported = false;
    public $importedRecipe = null;

    public function preview()
    {
        $this->reset(['parsedData', 'validationErrors', 'validSections', 'imported', 'importedRecipe']);

        if (empty(trim($this->markdown))) {
            $this->validationErrors = ['El texto está vacío.'];
            return;
        }

        $parser = new RmsV2Parser();
        $result = $parser->validate($this->markdown);

        if (!$result->valid) {
            $this->validationErrors = $result->errors;
        } else {
            $this->parsedData = $result->data;
            $this->validSections = [
                'Nombre' => $result->data['name'] ?? '—',
                'Slug' => $result->data['slug'] ?? '—',
                'Categoría' => $result->data['category'] ?? '—',
                'Dificultad' => str_repeat('⭐', $result->data['difficulty'] ?? 1),
                'Porciones' => $result->data['servings'] ?? '—',
                'Preparación' => ($result->data['prep_time'] ?? 0) . ' min',
                'Cocción' => ($result->data['cook_time'] ?? 0) . ' min',
                'Total' => ($result->data['total_time'] ?? 0) . ' min',
                'Ingredientes' => count($result->data['ingredients'] ?? []),
                'Equipo' => count($result->data['equipment'] ?? []),
                'Pasos' => count($result->data['steps'] ?? []),
                'Variantes' => count($result->data['variants'] ?? []),
                'Adaptaciones' => count($result->data['adaptations'] ?? []),
                'Conceptos' => count($result->data['concepts'] ?? []),
                'Problemas' => count($result->data['errors_list'] ?? []),
            ];
        }
    }

    public function import()
    {
        if (empty(trim($this->markdown))) return;

        $parser = new RmsV2Parser();
        $result = $parser->validate($this->markdown);

        if (!$result->valid) {
            $this->validationErrors = $result->errors;
            return;
        }

        $importer = new RmsV2Importer();
        $recipe = $importer->import($result->data, $this->markdown);

        $this->imported = true;
        $this->importedRecipe = $recipe;
    }

    public function render()
    {
        return view('components.⚡rms-v2-importer');
    }
};
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center gap-3 mb-2">
        <h1 class="text-3xl font-bold text-gray-900">📥 Importar Receta (RMS v2.0)</h1>
        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full font-medium">v2.0</span>
    </div>
    <p class="text-gray-500 mb-8">Pega tu receta en formato RMS v2.0. El sistema validará todas las secciones antes de importar.</p>

    @if($imported && $importedRecipe)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center mb-8">
            <div class="text-5xl mb-4">✅</div>
            <h2 class="text-2xl font-bold text-green-800 mb-2">¡Receta importada!</h2>
            <p class="text-green-700 mb-6">{{ $importedRecipe->name }}</p>
            <div class="flex justify-center gap-4">
                <a href="/recetas/{{ $importedRecipe->slug }}"
                   class="bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors">Ver receta →</a>
                <button wire:click="$set('imported', false)"
                   class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold border border-gray-200 hover:bg-gray-50 transition-colors">Importar otra</button>
            </div>
        </div>
    @endif

    @if(!empty($validationErrors))
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
            <h3 class="font-bold text-red-800 mb-3">⚠️ Errores de validación ({{ count($validationErrors) }})</h3>
            <ul class="space-y-1">
                @foreach($validationErrors as $error)
                    <li class="text-sm text-red-700 flex items-start gap-2">
                        <span class="text-red-400 mt-0.5">✗</span>
                        <span>{{ $error }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 {{ $parsedData ? 'lg:grid-cols-2' : '' }} gap-8">
        <div>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700 text-sm">📝 Markdown (RMS v2.0)</h2>
                    <button wire:click="preview" class="bg-purple-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                        🔍 Validar
                    </button>
                </div>
                <textarea wire:model="markdown" rows="25"
                    placeholder="Pega aquí tu receta RMS v2.0..." 
                    class="w-full px-5 py-4 text-sm font-mono border-0 resize-none focus:ring-0 focus:outline-none"></textarea>
            </div>
        </div>
        @if($parsedData)
            <div>
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden sticky top-20">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-700 text-sm">👁️ Validación exitosa</h2>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">16 secciones OK</span>
                    </div>
                    <div class="px-5 py-4 space-y-3 max-h-[600px] overflow-y-auto text-sm">
                        @foreach($validSections as $label => $value)
                            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                <span class="text-gray-500">{{ $label }}</span>
                                <span class="font-medium text-gray-800">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                        <button wire:click="import"
                            class="w-full bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                            ✅ Importar receta
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
