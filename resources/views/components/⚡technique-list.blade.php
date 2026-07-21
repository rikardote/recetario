<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Technique;

#[Layout('layouts.app', ['title' => 'Técnicas'])]
new class extends Component
{
    public function render()
    {
        return view('components.⚡technique-list', [
            'techniques' => Technique::all(),
        ]);
    }
};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Técnicas Culinarias</h1>
    <p class="text-gray-500 mb-10 max-w-2xl">Domina las técnicas fundamentales de la cocina a presión y cocina con criterio técnico.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($techniques as $technique)
            <div class="border border-gray-100 rounded-2xl p-6 hover:border-orange-200 hover:shadow-lg transition-all">
                <h3 class="font-semibold text-gray-900 text-lg mb-3">{{ $technique->name }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-4">{{ $technique->description }}</p>
                @if($technique->steps)
                    <details class="group">
                        <summary class="text-sm font-medium text-orange-600 cursor-pointer hover:text-orange-700">Ver pasos</summary>
                        <pre class="mt-3 text-xs text-gray-600 bg-gray-50 rounded-xl p-4 whitespace-pre-wrap">{{ $technique->steps }}</pre>
                    </details>
                @endif
            </div>
        @endforeach
    </div>
</div>
