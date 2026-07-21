<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\InstantPotFunction;

#[Layout('layouts.app', ['title' => 'Funciones Instant Pot'])]
new class extends Component
{
    public function render()
    {
        return view('components.⚡function-list', [
            'functions' => InstantPotFunction::all(),
        ]);
    }
};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Funciones de la Instant Pot</h1>
    <p class="text-gray-500 mb-10 max-w-2xl">Cada función tiene su propósito. Conocerlas permite aprovechar al máximo tu olla.</p>

    <div class="space-y-6">
        @foreach($functions as $func)
            <div class="border border-gray-100 rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 text-lg mb-3">⚡ {{ $func->name }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $func->description }}</p>
                @if($func->when_to_use)
                    <div class="bg-blue-50 rounded-xl p-4 text-sm">
                        <strong class="text-blue-700">📌 Cuándo utilizar:</strong>
                        <p class="text-blue-800 mt-1">{{ $func->when_to_use }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
