<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manual Maestro de Cocina para Instant Pot">

    <title>{{ $title ?? 'Recetario Instant Pot' }} | Manual Maestro</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white text-gray-900 antialiased">

    <nav class="border-b border-gray-100 bg-white/80 backdrop-blur-md sticky top-0 z-50" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900 tracking-tight hover:text-orange-600 transition-colors">
                        <span class="text-orange-500">⚡</span> Recetario
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-1">
                    <a href="/" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">Inicio</a>
                    <a href="/recetas" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">Recetas</a>
                    <a href="/tecnicas" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">Técnicas</a>
                    <a href="/conceptos" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">Conceptos</a>
                    <a href="/funciones" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">Funciones IP</a>
                    <a href="/accesorios" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">Accesorios</a>
                    <a href="/comparar" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">Comparar</a>
                    <a href="/importar" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">Importar</a>
                    <a href="/importar-v2" class="ml-2 px-4 py-2 text-sm font-semibold bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors shadow-sm">+ Nueva v2</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div x-show="open" x-cloak class="md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-2 space-y-1">
                <a href="/" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg">Inicio</a>
                <a href="/recetas" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg">Recetas</a>
                <a href="/tecnicas" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg">Técnicas</a>
                <a href="/conceptos" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg">Conceptos</a>
                <a href="/funciones" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg">Funciones IP</a>
                <a href="/accesorios" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg">Accesorios</a>
                <a href="/comparar" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg">Comparar</a>
                <a href="/importar" class="block px-3 py-2 text-sm font-semibold text-orange-600 hover:bg-orange-50 rounded-lg" onclick="open=false">+ Nueva receta</a>
                <a href="/importar-v2" class="block px-3 py-2 text-sm font-semibold text-purple-600 hover:bg-purple-50 rounded-lg" onclick="open=false">+ Nueva v2</a>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-100 bg-gray-50 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-3"><span class="text-orange-500">⚡</span> Recetario Instant Pot</h3>
                    <p class="text-sm text-gray-500 leading-relaxed max-w-md">Manual Maestro de Cocina para Instant Pot. Aprende técnicas culinarias, domina la cocción a presión y crea tus propias recetas con criterio técnico.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Secciones</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="/recetas" class="hover:text-gray-900">Recetas</a></li>
                        <li><a href="/tecnicas" class="hover:text-gray-900">Técnicas</a></li>
                        <li><a href="/conceptos" class="hover:text-gray-900">Conceptos</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Niveles</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li>⭐ Principiante</li>
                        <li>⭐⭐ Básico</li>
                        <li>⭐⭐⭐ Intermedio</li>
                        <li>⭐⭐⭐⭐ Avanzado</li>
                        <li>⭐⭐⭐⭐⭐ Experto</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200 text-center text-xs text-gray-400">
                Recetario Instant Pot — Manual gratuito de cocina a presión
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
