<?php

namespace Database\Seeders;

use App\Models\Technique;
use Illuminate\Database\Seeder;

class TechniqueSeeder extends Seeder
{
    public function run(): void
    {
        $techniques = [
            [
                'name' => 'Sellado',
                'slug' => 'sellado',
                'description' => 'Técnica que consiste en dorar la superficie de la carne a alta temperatura usando la función Sauté. La reacción de Maillard genera una costra dorada que aporta sabor profundo y color atractivo.',
                'steps' => "1. Calentar la olla en Sauté (Normal o Más).\n2. Agregar aceite con punto de humo alto.\n3. Colocar la carne y no mover durante 3-4 minutos.\n4. Voltear y dorar el otro lado.\n5. Retirar y continuar con la receta.",
            ],
            [
                'name' => 'Liberación Natural',
                'slug' => 'liberacion-natural',
                'description' => 'Consiste en permitir que la presión disminuya gradualmente sin intervención. La válvula permanece cerrada y la temperatura baja lentamente.',
                'steps' => "1. Al terminar el tiempo de cocción, no tocar la válvula.\n2. Esperar de 10 a 25 minutos según la receta.\n3. El pin flotante bajará cuando la presión se haya liberado.\n4. Abrir la tapa con cuidado.",
            ],
            [
                'name' => 'Liberación Rápida',
                'slug' => 'liberacion-rapida',
                'description' => 'Liberación manual del vapor girando la válvula a la posición de ventilación. Detiene la cocción de forma inmediata.',
                'steps' => "1. Usar una cuchara de madera o utensilio largo.\n2. Girar la válvula a posición 'Venting'.\n3. Mantener rostro y manos alejados del vapor.\n4. Esperar a que el pin flotante baje completamente.\n5. Abrir la tapa.",
            ],
            [
                'name' => 'Pot in Pot',
                'slug' => 'pot-in-pot',
                'description' => 'Método de cocción donde se coloca un recipiente dentro de la olla principal sobre el trivet con agua en el fondo.',
                'steps' => "1. Agregar 1-2 tazas de agua en la olla principal.\n2. Colocar el trivet.\n3. Poner el molde con los alimentos sobre el trivet.\n4. Cerrar y cocinar a presión.\n5. Usar liberación natural para postres.",
            ],
            [
                'name' => 'Deglasar',
                'slug' => 'deglasar',
                'description' => 'Proceso de disolver los restos dorados pegados al fondo de la olla agregando un líquido (caldo, vino, agua) mientras se raspa con una cuchara de madera.',
                'steps' => "1. Después de sellar, retirar la proteína.\n2. Verter el líquido frío en la olla caliente.\n3. Raspar el fondo con cuchara de madera.\n4. Los restos se disolverán y aportarán sabor al plato.",
            ],
            [
                'name' => 'Reducción',
                'slug' => 'reduccion',
                'description' => 'Concentración de sabores mediante evaporación de líquidos usando la función Sauté después de la cocción a presión.',
                'steps' => "1. Retirar los sólidos de la olla.\n2. Activar Sauté en modo Normal.\n3. Dejar hervir el líquido sin tapa.\n4. Revolver ocasionalmente hasta obtener la consistencia deseada.",
            ],
            [
                'name' => 'Marinado',
                'slug' => 'marinado',
                'description' => 'Proceso de sumergir alimentos en una mezcla de ácidos, aceites y condimentos antes de cocinar para mejorar sabor y textura.',
                'steps' => "1. Mezclar ácido, aceite, hierbas y especias.\n2. Sumergir la proteína completamente.\n3. Refrigerar mínimo 30 minutos (ideal 2-8 horas).\n4. Escurrir antes de cocinar.",
            ],
            [
                'name' => 'Cocción por capas',
                'slug' => 'coccion-por-capas',
                'description' => 'Técnica donde ingredientes con diferentes tiempos de cocción se agregan en momentos distintos o se colocan en posiciones estratégicas dentro de la olla.',
                'steps' => "1. Identificar tiempos de cocción de cada ingrediente.\n2. Colocar los de mayor tiempo en el fondo.\n3. Agregar los más delicados al final o en la parte superior.\n4. Usar trivet para separar capas si es necesario.",
            ],
        ];

        foreach ($techniques as $t) {
            Technique::firstOrCreate(["slug" => $t["slug"]], $t);
        }
    }
}
