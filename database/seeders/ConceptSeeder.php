<?php

namespace Database\Seeders;

use App\Models\Concept;
use Illuminate\Database\Seeder;

class ConceptSeeder extends Seeder
{
    public function run(): void
    {
        $concepts = [
            [
                'name' => 'Reacción de Maillard',
                'slug' => 'reaccion-de-maillard',
                'description' => 'Reacción química entre aminoácidos y azúcares reductores que ocurre al calentar alimentos a temperaturas superiores a 140°C. Produce el dorado característico y cientos de compuestos de sabor y aroma. Es la responsable del color y sabor de la carne sellada, el pan tostado y el café. No debe confundirse con la caramelización, que solo involucra azúcares.',
            ],
            [
                'name' => 'Caramelización',
                'slug' => 'caramelizacion',
                'description' => 'Proceso de oxidación de azúcares al ser calentados. A diferencia de la reacción de Maillard, solo involucra azúcares. Produce color marrón y sabores dulces y complejos. En la cocción a presión, ocurre durante el salteado inicial de verduras como cebolla y zanahoria.',
            ],
            [
                'name' => 'Colágeno',
                'slug' => 'colageno',
                'description' => 'Proteína estructural presente en tejidos conectivos de carnes. Con calor húmedo prolongado (como en la cocción a presión), se convierte en gelatina, aportando textura sedosa, cuerpo a los líquidos y sensación untuosa. Cortes con alto colágeno (falda, chamorro, cachete) son ideales para Instant Pot.',
            ],
            [
                'name' => 'Punto de humo',
                'slug' => 'punto-de-humo',
                'description' => 'Temperatura a la cual un aceite comienza a descomponerse y producir humo visible. Al superar este punto, el aceite genera compuestos tóxicos y sabores desagradables. Para sellar carnes en Sauté, usar aceites con punto de humo alto (>200°C) como aguacate, canola o ghee.',
            ],
            [
                'name' => 'Fondo',
                'slug' => 'fondo',
                'description' => 'Líquido concentrado obtenido de la cocción prolongada de huesos, vegetales y aromáticos. En la cocina profesional es la base de salsas, sopas y guisos. La Instant Pot permite preparar fondos en 45-60 minutos que tradicionalmente tomarían 4-8 horas.',
            ],
        ];

        foreach ($concepts as $c) {
            Concept::firstOrCreate(["slug" => $c["slug"]], $c);
        }
    }
}
