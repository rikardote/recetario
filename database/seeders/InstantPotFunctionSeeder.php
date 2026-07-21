<?php

namespace Database\Seeders;

use App\Models\InstantPotFunction;
use Illuminate\Database\Seeder;

class InstantPotFunctionSeeder extends Seeder
{
    public function run(): void
    {
        $functions = [
            [
                'name' => 'Pressure Cook',
                'slug' => 'pressure-cook',
                'description' => 'Función principal de cocción a presión. Permite cocinar alimentos en una fracción del tiempo tradicional. La presión elevada incrementa el punto de ebullición del agua, acelerando la transferencia de calor.',
                'when_to_use' => 'Para carnes, legumbres, arroces, sopas, guisos y cualquier alimento que requiera cocción húmeda prolongada.',
            ],
            [
                'name' => 'Sauté',
                'slug' => 'saute',
                'description' => 'Función de salteado que calienta el fondo de la olla sin presión. Equivale a cocinar en una sartén u olla convencional con temperatura regulable.',
                'when_to_use' => 'Para sellar carnes, sofreír aromáticos, reducir salsas, dorar especias y deglasar antes o después de la cocción a presión.',
            ],
            [
                'name' => 'Steam',
                'slug' => 'steam',
                'description' => 'Función de cocción al vapor. Genera vapor constante a alta temperatura para cocinar alimentos sin sumergirlos en líquido.',
                'when_to_use' => 'Para verduras, pescados, dumplings, tamales y alimentos que se benefician de cocción suave sin contacto directo con agua.',
            ],
            [
                'name' => 'Rice',
                'slug' => 'rice',
                'description' => 'Programa automático para arroz blanco. Ajusta tiempo y presión para grano perfecto.',
                'when_to_use' => 'Exclusivamente para arroz blanco de grano largo o medio. No usar para arroz integral.',
            ],
            [
                'name' => 'Slow Cook',
                'slug' => 'slow-cook',
                'description' => 'Función de cocción lenta sin presión. Similar a una olla de cocción lenta tradicional.',
                'when_to_use' => 'Para recetas que requieran cocción prolongada a baja temperatura, o cuando se desea una textura diferente.',
            ],
            [
                'name' => 'Keep Warm',
                'slug' => 'keep-warm',
                'description' => 'Mantiene los alimentos a temperatura segura después de la cocción.',
                'when_to_use' => 'Automático al finalizar cualquier ciclo. Se puede desactivar si no se desea.',
            ],
        ];

        foreach ($functions as $f) {
            InstantPotFunction::create($f);
        }
    }
}
