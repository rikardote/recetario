<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Pechuga de pollo', 'slug' => 'pechuga-de-pollo', 'description' => 'Corte magro de la pechuga del pollo. Cocción rápida.', 'storage_info' => 'Refrigerar máximo 2 días. Congelar hasta 6 meses.', 'usage_info' => 'Ideal para deshebrar, cortar en cubos o cocinar entera.'],
            ['name' => 'Muslo de pollo', 'slug' => 'muslo-de-pollo', 'description' => 'Carne oscura con mayor contenido de grasa intramuscular.', 'storage_info' => 'Refrigerar máximo 2 días. Congelar hasta 6 meses.', 'usage_info' => 'La opción más recomendable para cocción a presión por su jugosidad.'],
            ['name' => 'Arroz blanco', 'slug' => 'arroz-blanco', 'description' => 'Arroz de grano largo o medio.', 'storage_info' => 'Almacenar en lugar seco y fresco. Una vez cocido, refrigerar máximo 4 días.', 'usage_info' => 'Relación 1:1 agua/arroz en Instant Pot. Enjuagar antes de cocinar.'],
            ['name' => 'Frijol negro', 'slug' => 'frijol-negro', 'description' => 'Legumbre de color negro, rica en proteína y fibra.', 'storage_info' => 'Secos: lugar fresco y seco hasta 1 año. Cocidos: refrigerar 5 días.', 'usage_info' => 'No requieren remojo en Instant Pot. Cocinar 25-30 minutos a presión.'],
            ['name' => 'Cebolla', 'slug' => 'cebolla', 'description' => 'Bulbo aromático base de múltiples preparaciones.', 'storage_info' => 'Lugar fresco, seco y oscuro. No refrigerar.', 'usage_info' => 'Sofreír antes de la cocción a presión para desarrollar dulzor.'],
            ['name' => 'Ajo', 'slug' => 'ajo', 'description' => 'Bulbo aromático intenso.', 'storage_info' => 'Lugar fresco y seco. No refrigerar.', 'usage_info' => 'Agregar al sofrito. El ajo quemado amarga: controlar temperatura en Sauté.'],
            ['name' => 'Caldo de pollo', 'slug' => 'caldo-de-pollo', 'description' => 'Líquido base para cocciones.', 'storage_info' => 'Refrigerar máximo 4 días. Congelar en porciones.', 'usage_info' => 'Siempre preferir caldo sobre agua: aporta sabor y cuerpo.'],
            ['name' => 'Papa', 'slug' => 'papa', 'description' => 'Tubérculo versátil de cocción media.', 'storage_info' => 'Lugar fresco, seco y oscuro. No refrigerar.', 'usage_info' => 'Cortar en trozos uniformes. Cocinar 4-6 minutos a presión.'],
            ['name' => 'Zanahoria', 'slug' => 'zanahoria', 'description' => 'Raíz dulce de color naranja.', 'storage_info' => 'Refrigerar en bolsa perforada hasta 2 semanas.', 'usage_info' => 'Aporta dulzor natural. Cocinar 3-4 minutos a presión.'],
            ['name' => 'Aceite de oliva', 'slug' => 'aceite-de-oliva', 'description' => 'Aceite vegetal extraído de aceitunas.', 'storage_info' => 'Lugar fresco y oscuro. No refrigerar.', 'usage_info' => 'Usar para sofritos ligeros. Para sellar a alta temperatura, preferir aceite con punto de humo alto.'],
            ['name' => 'Sal', 'slug' => 'sal', 'description' => 'Condimento mineral esencial.', 'storage_info' => 'Lugar seco.', 'usage_info' => 'Salar en capas durante la cocción, no solo al final.'],
            ['name' => 'Pimienta negra', 'slug' => 'pimienta-negra', 'description' => 'Especia molida o en grano.', 'storage_info' => 'Lugar seco y oscuro.', 'usage_info' => 'Recién molida aporta mayor aroma y sabor.'],
            ['name' => 'Jitomate', 'slug' => 'jitomate', 'description' => 'Fruto ácido base de salsas.', 'storage_info' => 'No refrigerar. Madurar a temperatura ambiente.', 'usage_info' => 'Asar o sofreír antes de agregar líquidos.'],
        ];

        foreach ($ingredients as $i) {
            Ingredient::firstOrCreate(["slug" => $i["slug"]], $i);
        }
    }
}
