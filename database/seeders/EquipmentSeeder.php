<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Instant Pot',
                'slug' => 'instant-pot',
                'description' => 'Olla de presión eléctrica multifunción.',
                'when_to_use' => 'Para todas las recetas que requieran cocción a presión, vapor, salteado o cocción lenta.',
                'when_not_to_use' => 'No usar sin líquido suficiente (mínimo 1 taza). No llenar más de 2/3 de su capacidad.',
            ],
            [
                'name' => 'Trivet',
                'slug' => 'trivet',
                'description' => 'Rejilla metálica incluida con la Instant Pot.',
                'when_to_use' => 'Para cocinar al vapor, elevar alimentos del líquido, método Pot-in-Pot.',
                'when_not_to_use' => 'No usar cuando los alimentos deban estar en contacto directo con el líquido.',
            ],
            [
                'name' => 'Canastilla',
                'slug' => 'canastilla',
                'description' => 'Canasta de acero inoxidable para vapor.',
                'when_to_use' => 'Para verduras al vapor, huevos duros, alimentos pequeños.',
                'when_not_to_use' => 'Para líquidos o salsas que puedan escurrir.',
            ],
            [
                'name' => 'Molde Pot-in-Pot',
                'slug' => 'molde-pot-in-pot',
                'description' => 'Molde de acero inoxidable que se coloca dentro de la olla.',
                'when_to_use' => 'Para postres, panes al vapor, recetas que requieran molde.',
                'when_not_to_use' => 'No exceder la altura máxima que permita cerrar la tapa.',
            ],
            [
                'name' => 'Termómetro',
                'slug' => 'termometro',
                'description' => 'Termómetro de cocina digital.',
                'when_to_use' => 'Para verificar temperatura interna de carnes, punto exacto.',
                'when_not_to_use' => 'No dejar dentro de la olla durante la cocción a presión.',
            ],
            [
                'name' => 'Licuadora',
                'slug' => 'licuadora',
                'description' => 'Licuadora de inmersión o de vaso.',
                'when_to_use' => 'Para cremas, salsas, sopas licuadas después de la cocción.',
                'when_not_to_use' => 'No introducir en la olla caliente si es de vaso; usar de inmersión.',
            ],
        ];

        foreach ($items as $item) {
            Equipment::firstOrCreate(["slug" => $item["slug"]], $item);
        }
    }
}
