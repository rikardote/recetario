<?php

use Livewire\Component;
use App\Models\Equipment;

new class extends Component {};
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Accesorios</h1>
    <p class="text-gray-500 mb-10 max-w-2xl">Cada accesorio tiene un propósito. Aprende cuándo usarlos.</p>
    <div class="space-y-6">
        @foreach(Equipment::all() as $eq)
            <div class="border border-gray-100 rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 text-lg mb-3">{{ $eq->name }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ $eq->description }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($eq->when_to_use)<div class="bg-green-50 rounded-xl p-4 text-sm"><strong class="text-green-700">✅ Usar:</strong><p class="text-green-800 mt-1">{{ $eq->when_to_use }}</p></div>@endif
                    @if($eq->when_not_to_use)<div class="bg-red-50 rounded-xl p-4 text-sm"><strong class="text-red-700">❌ No usar:</strong><p class="text-red-800 mt-1">{{ $eq->when_not_to_use }}</p></div>@endif
                </div>
            </div>
        @endforeach
    </div>
</div>
