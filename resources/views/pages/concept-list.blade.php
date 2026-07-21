<?php

use Livewire\Component;
use App\Models\Concept;

new class extends Component {};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Conceptos Culinarios</h1>
    <p class="text-gray-500 mb-10 max-w-2xl">Comprende los principios científicos detrás de cada técnica.</p>
    <div class="space-y-8">
        @foreach(Concept::all() as $concept)
            <div class="border border-gray-100 rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 text-lg mb-3">{{ $concept->name }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $concept->description }}</p>
            </div>
        @endforeach
    </div>
</div>
