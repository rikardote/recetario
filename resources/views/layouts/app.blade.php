<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manual Maestro de Cocina para Instant Pot">

    <title>{{ $title ?? 'Recetario Instant Pot' }} | Manual Maestro</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script>
        // Dark mode init — runs before Alpine/any render
        (function() {
            var dark = localStorage.getItem('dark');
            if (dark === 'true' || (!dark && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <style>
        html { transition: background-color .3s, color .3s; }
        html.dark { color-scheme: dark; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">

    <nav class="border-b border-gray-100 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md sticky top-0 z-50" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900 dark:text-white tracking-tight hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                        <span class="text-orange-500">⚡</span> Recetario
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-1">
                    <a href="/" class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Inicio</a>
                    <a href="/recetas" class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Recetas</a>
                    <a href="/tecnicas" class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Técnicas</a>
                    <a href="/conceptos" class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Conceptos</a>
                    <a href="/funciones" class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Funciones IP</a>
                    <a href="/accesorios" class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Accesorios</a>
                    <a href="/comparar" class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Comparar</a>

                    <button @click="document.documentElement.classList.toggle('dark');localStorage.setItem('dark',document.documentElement.classList.contains('dark'))" class="ml-3 p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Cambiar modo oscuro">
                        <svg id="icon-sun" class="w-5 h-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg id="icon-moon" class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>

                    <a href="/importar" class="ml-2 px-4 py-2 text-sm font-semibold bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-colors shadow-sm">+ Nueva</a>
                </div>

                <div class="md:hidden flex items-center">
                    <button @click="document.documentElement.classList.toggle('dark');localStorage.setItem('dark',document.documentElement.classList.contains('dark'))" class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 mr-1 transition-colors" title="Cambiar modo oscuro">
                        <svg id="icon-sun-m" class="w-5 h-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg id="icon-moon-m" class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                    <button @click="open = !open" class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="open" x-cloak class="md:hidden border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
            <div class="px-4 py-2 space-y-1">
                <a href="/" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg">Inicio</a>
                <a href="/recetas" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg">Recetas</a>
                <a href="/tecnicas" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg">Técnicas</a>
                <a href="/conceptos" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg">Conceptos</a>
                <a href="/funciones" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg">Funciones IP</a>
                <a href="/accesorios" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg">Accesorios</a>
                <a href="/comparar" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg">Comparar</a>
                <a href="/importar" class="block px-3 py-2 text-sm font-semibold text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg">+ Nueva receta</a>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3"><span class="text-orange-500">⚡</span> Recetario Instant Pot</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-md">Manual Maestro de Cocina para Instant Pot.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Secciones</h4>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li><a href="/recetas" class="hover:text-gray-900 dark:hover:text-white">Recetas</a></li>
                        <li><a href="/tecnicas" class="hover:text-gray-900 dark:hover:text-white">Técnicas</a></li>
                        <li><a href="/conceptos" class="hover:text-gray-900 dark:hover:text-white">Conceptos</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Niveles</h4>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li>⭐ Principiante</li>
                        <li>⭐⭐ Básico</li>
                        <li>⭐⭐⭐ Intermedio</li>
                        <li>⭐⭐⭐⭐ Avanzado</li>
                        <li>⭐⭐⭐⭐⭐ Experto</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-800 text-center text-xs text-gray-400 dark:text-gray-500">Recetario Instant Pot — Manual gratuito de cocina a presión</div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
