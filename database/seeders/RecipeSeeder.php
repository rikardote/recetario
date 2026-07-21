<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeAdaptation;
use App\Models\RecipeConcept;
use App\Models\RecipeError;
use App\Models\RecipeImage;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\RecipeVariant;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPolloBBQ();
        $this->seedFrijolesNegros();
        $this->seedArrozBlanco();
        $this->seedSopaPollo();
        $this->seedPastaBolognesa();
    }

    private function seedPolloBBQ(): void
    {
        $recipe = Recipe::create([
            'category_id' => Category::where('slug', 'pollo')->first()->id,
            'name' => 'Pollo BBQ en Instant Pot',
            'slug' => 'pollo-bbq',
            'description' => 'Muslos de pollo jugosos cubiertos con una salsa BBQ casera espesa y brillante, preparados completamente en la Instant Pot.',
            'objective' => 'Preparar un pollo muy jugoso con una salsa BBQ espesa y brillante utilizando ingredientes fáciles de conseguir. El resultado debe ser carne que se desprenda fácilmente con un tenedor, con una salsa caramelizada de sabor profundo.',
            'prep_time' => 15,
            'cook_time' => 25,
            'total_time' => 40,
            'servings' => 4,
            'difficulty' => 2,
            'cost' => 'Medio',
            'result_texture' => 'Carne suave que se deshebra con facilidad. Salsa espesa y brillante.',
            'result_color' => 'Rojo oscuro con tonos caoba.',
            'result_consistency' => 'Salsa lo suficientemente espesa para adherirse a la carne sin escurrir.',
            'result_temperature' => 'Servir caliente a 65-70°C.',
            'result_flavor' => 'Equilibrio entre dulce, ahumado y ácido. Notas de especias.',
            'storage_refrigeration' => 'Refrigerar en recipiente hermético hasta 4 días.',
            'storage_freezing' => 'Congelar en porciones hasta 3 meses. Descongelar en refrigeración.',
            'storage_reheating' => 'Recalentar en microondas o en Sauté a fuego bajo, agregando una cucharada de agua si es necesario.',
            'pressure_cook_time' => 12,
            'pressure_release' => 'natural',
            'pressure_release_time' => 10,
            'saute_time' => 15,
            'chef_notes' => 'Utilizar muslos produce una carne más jugosa debido a su mayor contenido de grasa intramuscular en comparación con la pechuga. La liberación natural es crítica: una liberación rápida provocará que la carne se tense y pierda jugosidad.',
            'is_published' => true,
        ]);

        // Tags
        $recipe->tags()->attach(Tag::whereIn('slug', ['facil', 'alto-en-proteina', 'una-olla', 'sin-gluten'])->pluck('id'));

        // Equipment
        $recipe->equipment()->attach(
            Equipment::where('slug', 'instant-pot')->first()->id
        );

        // Ingredients
        $ingredients = [
            ['ingredient_id' => Ingredient::where('slug', 'muslo-de-pollo')->first()->id, 'category' => 'proteinas', 'quantity' => 4, 'unit' => 'piezas', 'is_recommended' => true],
            ['ingredient_id' => Ingredient::where('slug', 'cebolla')->first()->id, 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza', 'is_recommended' => false],
            ['ingredient_id' => Ingredient::where('slug', 'ajo')->first()->id, 'category' => 'condimentos', 'quantity' => 3, 'unit' => 'dientes', 'is_recommended' => false],
            ['ingredient_id' => Ingredient::where('slug', 'caldo-de-pollo')->first()->id, 'category' => 'liquidos', 'quantity' => 0.5, 'unit' => 'taza', 'is_recommended' => false],
        ];
        foreach ($ingredients as $ing) {
            RecipeIngredient::create(array_merge(['recipe_id' => $recipe->id], $ing));
        }

        // Steps
        $steps = [
            ['step_number' => 1, 'action' => 'Encender la Instant Pot en modo Sauté (Normal). Agregar una cucharada de aceite.', 'technical_fundament' => 'El modo Sauté permite calentar el fondo de la olla para dorar los alimentos antes de la cocción a presión. Es importante usar el nivel Normal, no Más, para evitar quemar el aceite.', 'what_to_observe' => 'El aceite debe estar brillante y fluido, no humeante. La pantalla mostrará "Hot" cuando alcance la temperatura.', 'common_errors' => 'Usar temperatura muy alta (Más) puede quemar el aceite y generar sabores amargos.'],
            ['step_number' => 2, 'action' => 'Sazonar los muslos con sal y pimienta. Colocarlos en la olla caliente con la piel hacia abajo. No mover durante 4 minutos.', 'technical_fundament' => 'El sellado genera la reacción de Maillard: los aminoácidos y azúcares de la carne reaccionan con el calor creando una costra dorada que aporta sabor profundo. No mover la carne permite que se forme esta costra correctamente.', 'what_to_observe' => 'La piel debe despegarse fácilmente del fondo cuando esté lista para voltear. Color dorado oscuro, no negro.', 'common_errors' => 'Mover la carne antes de tiempo impide la formación de la costra. Si el fondo está muy caliente y la piel se quema, reducir el nivel de Sauté.'],
            ['step_number' => 3, 'action' => 'Voltear los muslos y dorar 2 minutos adicionales. Retirar y reservar.', 'technical_fundament' => 'El segundo lado requiere menos tiempo porque la carne ya absorbió calor. Este sellado rápido es suficiente para desarrollar sabor.', 'what_to_observe' => 'Ligero dorado en el segundo lado.', 'common_errors' => 'Dejar la carne demasiado tiempo; solo se busca sellar, no cocinar completamente.'],
            ['step_number' => 4, 'action' => 'Agregar cebolla picada y ajo. Sofreír 3 minutos hasta que la cebolla esté translúcida.', 'technical_fundament' => 'El sofrito desarrolla dulzor en la cebolla mediante caramelización de sus azúcares naturales. El ajo libera compuestos aromáticos que forman la base de sabor. Además, la humedad de la cebolla ayuda a deglasar los restos dorados del fondo.', 'what_to_observe' => 'Cebolla translúcida con bordes dorados. Ajo fragante pero no quemado.', 'common_errors' => 'Quemar el ajo produce amargor irreversible. Agregarlo 1 minuto después de la cebolla.'],
            ['step_number' => 5, 'action' => 'Verter el caldo de pollo y raspar el fondo con cuchara de madera para desprender los restos dorados (deglasar). Agregar salsa BBQ y mezclar.', 'technical_fundament' => 'El deglasado recupera los compuestos de sabor pegados al fondo (fond de cocción). Si estos restos no se desprenden, pueden quemarse durante la cocción a presión y provocar el mensaje "Burn" en la Instant Pot.', 'what_to_observe' => 'El líquido debe adquirir un color marrón oscuro. No deben quedar residuos pegados en el fondo.', 'common_errors' => 'Si quedan restos pegados, la Instant Pot puede mostrar "Burn" y detener la cocción. Raspar minuciosamente.'],
            ['step_number' => 6, 'action' => 'Regresar los muslos a la olla. Cerrar la tapa, asegurar la válvula en "Sealing". Programar Pressure Cook en Alta presión por 12 minutos.', 'technical_fundament' => 'Los muslos requieren 12 minutos a alta presión para que el colágeno se convierta en gelatina. La alta presión eleva la temperatura de ebullición del agua a aproximadamente 121°C, acelerando la conversión del tejido conectivo.', 'what_to_observe' => 'La válvula debe estar firmemente en "Sealing". La olla emitirá un pitido al iniciar.', 'common_errors' => 'No verificar que la válvula esté en "Sealing": el vapor escapará y la receta no se cocinará correctamente.'],
            ['step_number' => 7, 'action' => 'Al terminar, permitir Liberación Natural durante 10 minutos. Luego liberar el vapor restante con cuidado.', 'technical_fundament' => 'La liberación natural es crítica para carnes: permite que las fibras musculares se relajen gradualmente y reabsorban los jugos. Una liberación rápida causa que los jugos internos hiervan violentamente y escapen, resultando en carne seca.', 'what_to_observe' => 'El pin flotante bajará solo después de la liberación natural. Al abrir, la carne debe verse brillante y jugosa.', 'common_errors' => 'Realizar liberación rápida inmediata: la carne quedará seca y fibrosa. Esperar siempre.'],
            ['step_number' => 8, 'action' => 'Retirar los muslos. Activar Sauté (Normal) y reducir la salsa durante 5-8 minutos hasta que espese y brille.', 'technical_fundament' => 'La reducción en Sauté evapora el exceso de agua concentrando los azúcares y proteínas de la salsa. La evaporación debe ser sin tapa para permitir que el vapor escape. La salsa está lista cuando al pasar una cuchara, el camino se mantiene visible brevemente (napado).', 'what_to_observe' => 'La salsa debe cubrir el dorso de una cuchara sin escurrir inmediatamente. Debe verse brillante, no opaca.', 'common_errors' => 'Reducir demasiado: la salsa se vuelve demasiado salada. Retirar del fuego cuando esté ligeramente más líquida de lo deseado, pues espesará al enfriar.'],
        ];
        foreach ($steps as $step) {
            RecipeStep::create(array_merge(['recipe_id' => $recipe->id], $step));
        }

        // Variants
        $variants = [
            ['name' => 'Versión picante', 'description' => 'Agregar chipotle en adobo picado o chile de árbol al sofrito.', 'ingredients_changes' => 'Agregar 2 chiles chipotles picados.', 'procedure_changes' => 'Incorporar los chiles junto con la cebolla en el paso 4.'],
            ['name' => 'Versión saludable', 'description' => 'Salsa BBQ sin azúcar refinada, usando puré de dátiles.', 'ingredients_changes' => 'Sustituir azúcar por 4 dátiles licuados con el caldo.', 'procedure_changes' => 'Licuar dátiles sin hueso con el caldo antes de agregar.'],
            ['name' => 'Versión con pechuga', 'description' => 'Usar pechuga en lugar de muslo.', 'ingredients_changes' => '4 pechugas de pollo sin piel.', 'procedure_changes' => 'Reducir tiempo de presión a 8 minutos. La pechuga requiere menos tiempo.'],
        ];
        foreach ($variants as $v) {
            RecipeVariant::create(array_merge(['recipe_id' => $recipe->id], $v));
        }

        // Adaptations
        $adaptations = [
            ['scenario' => '¿Cómo cambiar la receta si uso pollo congelado?', 'adaptation_text' => 'Agregar 5 minutos adicionales de cocción a presión (total 17 minutos). No es necesario sellar el pollo congelado; colocar directamente con los líquidos.'],
            ['scenario' => '¿Cómo adaptar para Instant Pot 8Qt?', 'adaptation_text' => 'Aumentar el caldo a 3/4 de taza para cubrir el fondo. Los tiempos de cocción no cambian.'],
            ['scenario' => '¿Cómo hacer media receta?', 'adaptation_text' => 'Reducir todos los ingredientes a la mitad. Mantener la misma cantidad de caldo (1/2 taza) para que la olla alcance presión. Los tiempos no cambian.'],
            ['scenario' => '¿Cómo duplicar la receta?', 'adaptation_text' => 'Duplicar ingredientes pero mantener el mismo tiempo de cocción. No llenar la olla más de 2/3 de su capacidad.'],
        ];
        foreach ($adaptations as $a) {
            RecipeAdaptation::create(array_merge(['recipe_id' => $recipe->id], $a));
        }

        // Concepts
        $conceptTexts = ['Sellado', 'Liberación Natural', 'Deglasado', 'Reducción', 'Reacción de Maillard', 'Cocción por capas'];
        foreach ($conceptTexts as $concept) {
            RecipeConcept::create(['recipe_id' => $recipe->id, 'concept_text' => $concept]);
        }

        // Errors
        $errors = [
            ['problem' => 'La salsa quedó muy líquida', 'possible_cause' => 'No se redujo el tiempo suficiente en Sauté.', 'solution' => 'Reducir 5-8 minutos adicionales en Sauté sin tapa hasta obtener consistencia deseada.'],
            ['problem' => 'El pollo quedó seco', 'possible_cause' => 'Se realizó liberación rápida en lugar de natural.', 'solution' => 'La liberación natural es obligatoria para carnes. En la próxima ocasión, esperar mínimo 10 minutos.'],
            ['problem' => 'La olla mostró "Burn"', 'possible_cause' => 'Restos pegados en el fondo después del sellado.', 'solution' => 'Deglasar completamente el fondo con caldo antes de cocinar a presión. Cancelar, abrir, raspar y reiniciar.'],
        ];
        foreach ($errors as $e) {
            RecipeError::create(array_merge(['recipe_id' => $recipe->id], $e));
        }
    }

    private function seedFrijolesNegros(): void
    {
        $recipe = Recipe::create([
            'category_id' => Category::where('slug', 'frijoles')->first()->id,
            'name' => 'Frijoles Negros desde Cero',
            'slug' => 'frijoles-negros',
            'description' => 'Frijoles negros perfectamente cocidos sin remojo previo, listos para servir como guarnición o base de otras preparaciones.',
            'objective' => 'Preparar frijoles negros de cocción uniforme, con caldo espeso y sabroso, sin necesidad de remojo previo, aprovechando al máximo la capacidad de la cocción a presión.',
            'prep_time' => 5,
            'cook_time' => 35,
            'total_time' => 40,
            'servings' => 6,
            'difficulty' => 1,
            'cost' => 'Bajo',
            'result_texture' => 'Frijoles suaves y cremosos. Caldo ligeramente espeso.',
            'result_color' => 'Negro intenso con caldo marrón oscuro.',
            'result_consistency' => 'Frijoles enteros pero suaves. Caldo con cuerpo.',
            'result_temperature' => 'Servir calientes a 70°C.',
            'result_flavor' => 'Sabor terroso con notas de ajo y comino.',
            'pressure_cook_time' => 30,
            'pressure_release' => 'natural',
            'pressure_release_time' => 15,
            'chef_notes' => 'Los frijoles no requieren remojo en Instant Pot. La cocción a presión hidrata y cocina los frijoles secos en 30 minutos. Agregar sal al inicio endurece la cáscara: sazonar después de la cocción.',
            'is_published' => true,
        ]);

        $recipe->tags()->attach(Tag::whereIn('slug', ['facil', 'economico', 'una-olla', 'sin-gluten', 'vegano', 'meal-prep'])->pluck('id'));

        $steps = [
            ['step_number' => 1, 'action' => 'Enjuagar 2 tazas de frijoles negros secos bajo agua fría. Revisar y retirar piedras o frijoles dañados.', 'technical_fundament' => 'El lavado elimina polvo y posibles residuos. La revisión previene daños en la válvula.', 'what_to_observe' => 'Agua clara después del enjuague.', 'common_errors' => 'Omitir la revisión: una piedra puede dañar la olla.'],
            ['step_number' => 2, 'action' => 'Colocar frijoles en la Instant Pot con 6 tazas de agua. Agregar 1/2 cebolla, 2 dientes de ajo y 1 hoja de laurel.', 'technical_fundament' => 'La relación 1:3 (frijoles:agua) es la óptima para cocción a presión sin remojo. Los aromáticos infusionan sabor durante la cocción.', 'what_to_observe' => 'El agua debe cubrir los frijoles por al menos 5 cm.', 'common_errors' => 'Usar menos agua: los frijoles absorben mucho líquido y pueden quedar secos. Agregar demasiada agua: caldo muy diluido.'],
            ['step_number' => 3, 'action' => 'Cerrar la tapa. Programar Pressure Cook en Alta presión por 30 minutos.', 'technical_fundament' => '30 minutos es el tiempo óptimo para frijoles negros secos sin remojo. Tiempos menores resultan en frijoles duros.', 'what_to_observe' => 'La olla debe sellar correctamente.', 'common_errors' => 'No cerrar bien la tapa: no alcanzará presión.'],
            ['step_number' => 4, 'action' => 'Liberación Natural de 15 minutos. Luego liberar vapor restante.', 'technical_fundament' => 'La liberación natural permite que los frijoles terminen de cocerse con el calor residual y evita que las cáscaras revienten por cambio brusco de presión.', 'what_to_observe' => 'Frijoles suaves al presionar uno entre los dedos.', 'common_errors' => 'Liberación rápida puede reventar las cáscaras.'],
            ['step_number' => 5, 'action' => 'Retirar cebolla y laurel. Agregar sal. Activar Sauté 3-5 minutos para espesar el caldo si se desea.', 'technical_fundament' => 'La sal después de la cocción permite que los frijoles se hidraten sin que la cáscara se endurezca. El Sauté final evapora exceso para caldo más espeso.', 'what_to_observe' => 'Caldo ligeramente espeso. Frijoles que se aplastan fácilmente.', 'common_errors' => 'Agregar sal antes de cocinar endurece la cáscara.'],
        ];
        foreach ($steps as $step) {
            RecipeStep::create(array_merge(['recipe_id' => $recipe->id], $step));
        }

        $recipe->concepts()->createMany([
            ['concept_text' => 'Cocción de legumbres'],
            ['concept_text' => 'Liberación Natural'],
        ]);
    }

    private function seedArrozBlanco(): void
    {
        $recipe = Recipe::create([
            'category_id' => Category::where('slug', 'arroz')->first()->id,
            'name' => 'Arroz Blanco Perfecto',
            'slug' => 'arroz-blanco-perfecto',
            'description' => 'Arroz blanco de grano suelto y cocción uniforme, preparado en minutos con la Instant Pot. La base para innumerables platillos.',
            'objective' => 'Obtener un arroz blanco de grano entero, suelto y perfectamente cocido. Sin grumos, sin exceso de humedad y sin granos reventados.',
            'prep_time' => 5,
            'cook_time' => 10,
            'total_time' => 15,
            'servings' => 4,
            'difficulty' => 1,
            'cost' => 'Bajo',
            'pressure_cook_time' => 4,
            'pressure_release' => 'natural',
            'pressure_release_time' => 10,
            'chef_notes' => 'La proporción 1:1 de arroz y agua es la diferencia clave con la cocción tradicional. La Instant Pot no evapora líquido, por lo que se usa exactamente la cantidad que el arroz absorberá. Enjuagar el arroz elimina almidón superficial que causaría grumos.',
            'is_published' => true,
        ]);
        $recipe->tags()->attach(Tag::whereIn('slug', ['facil', 'economico', 'una-olla', 'sin-gluten', 'vegano'])->pluck('id'));

        $steps = [
            ['step_number' => 1, 'action' => 'Enjuagar 2 tazas de arroz blanco bajo agua fría hasta que el agua salga clara.', 'technical_fundament' => 'El enjuague elimina el almidón superficial que provoca que los granos se peguen entre sí. Sin este paso, el arroz quedará pastoso.', 'what_to_observe' => 'Agua casi transparente después de 3-4 enjuagues.', 'common_errors' => 'No enjuagar produce un arroz gomoso y apelmazado.'],
            ['step_number' => 2, 'action' => 'Colocar arroz y 2 tazas de agua en la Instant Pot. Agregar sal y una cucharadita de aceite.', 'technical_fundament' => 'Proporción 1:1. La olla a presión no evapora líquido, por lo que se usa justo lo que el arroz absorberá. El aceite ayuda a mantener los granos separados.', 'what_to_observe' => 'El agua debe cubrir apenas el arroz.', 'common_errors' => 'Usar proporción 2:1 como en cocción tradicional resultará en arroz aguado.'],
            ['step_number' => 3, 'action' => 'Cerrar la tapa. Programar Pressure Cook en Alta presión por 4 minutos.', 'technical_fundament' => '4 minutos es suficiente para arroz blanco de grano largo. La presión acelera la hidratación del almidón.', 'what_to_observe' => 'Tapa bien sellada.', 'common_errors' => 'Tiempos mayores producen arroz sobrecocido y reventado.'],
            ['step_number' => 4, 'action' => 'Liberación Natural de 10 minutos. No interrumpir.', 'technical_fundament' => 'La liberación natural permite que el arroz termine de absorber el agua residual con calor suave, resultando en granos perfectamente hidratados.', 'what_to_observe' => 'Al abrir, el arroz debe verse esponjoso y sin agua visible.', 'common_errors' => 'Liberación rápida deja el arroz con núcleo duro.'],
            ['step_number' => 5, 'action' => 'Esponjar el arroz con un tenedor antes de servir.', 'technical_fundament' => 'Esponjar con tenedor separa los granos sin romperlos, liberando vapor atrapado.', 'what_to_observe' => 'Granos sueltos que no se pegan.', 'common_errors' => 'Usar cuchara compacta el arroz y lo vuelve pastoso.'],
        ];
        foreach ($steps as $step) {
            RecipeStep::create(array_merge(['recipe_id' => $recipe->id], $step));
        }
    }

    private function seedSopaPollo(): void
    {
        $recipe = Recipe::create([
            'category_id' => Category::where('slug', 'sopas')->first()->id,
            'name' => 'Caldo de Pollo Casero',
            'slug' => 'caldo-de-pollo-casero',
            'description' => 'Caldo de pollo dorado, aromático y con cuerpo, preparado en una fracción del tiempo tradicional gracias a la cocción a presión.',
            'objective' => 'Obtener un caldo de pollo con color dorado intenso, aroma profundo y sabor equilibrado en solo 45 minutos en lugar de las 2-4 horas tradicionales.',
            'prep_time' => 15,
            'cook_time' => 35,
            'total_time' => 50,
            'servings' => 8,
            'difficulty' => 1,
            'cost' => 'Bajo',
            'pressure_cook_time' => 30,
            'pressure_release' => 'natural',
            'pressure_release_time' => 15,
            'chef_notes' => 'La cocción a presión extrae colágeno y minerales de los huesos en 30 minutos que tradicionalmente tomarían horas. El sellado inicial de los huesos y verduras aporta color dorado y sabor más complejo al caldo.',
            'is_published' => true,
        ]);

        $steps = [
            ['step_number' => 1, 'action' => 'Activar Sauté. Dorar 4 piezas de pollo con hueso (muslos o alas) hasta que tengan color dorado en todos lados.', 'technical_fundament' => 'El sellado genera reacción de Maillard en la carne y huesos, creando compuestos que darán color y sabor profundo al caldo.', 'what_to_observe' => 'Dorado parejo sin zonas negras.', 'common_errors' => 'No dorar suficiente: caldo pálido y de sabor plano.'],
            ['step_number' => 2, 'action' => 'Agregar cebolla partida, 2 zanahorias, 2 ramas de apio y 3 dientes de ajo. Sofreír 3 minutos.', 'technical_fundament' => 'Los vegetales caramelizados aportan dulzor natural y notas aromáticas al fondo del caldo.', 'what_to_observe' => 'Verduras con bordes dorados.', 'common_errors' => 'Agregar las verduras crudas produce un caldo menos aromático.'],
            ['step_number' => 3, 'action' => 'Agregar 8 tazas de agua, sal y hierbas. Cerrar tapa. Pressure Cook Alta 30 minutos.', 'technical_fundament' => '30 minutos a presión son suficientes para extraer colágeno, minerales y sabor de huesos y vegetales.', 'what_to_observe' => 'Agua cubriendo completamente los ingredientes.', 'common_errors' => 'Exceder el nivel máximo de llenado (2/3 de la olla).'],
            ['step_number' => 4, 'action' => 'Liberación Natural 15 minutos. Colar el caldo y deshebrar la carne del pollo.', 'technical_fundament' => 'Colar separa los sólidos dando un caldo limpio. La carne de pollo cocida a presión se deshebra fácilmente.', 'what_to_observe' => 'Caldo limpio y dorado.', 'common_errors' => 'No colar deja impurezas.'],
        ];
        foreach ($steps as $step) {
            RecipeStep::create(array_merge(['recipe_id' => $recipe->id], $step));
        }
    }

    private function seedPastaBolognesa(): void
    {
        $recipe = Recipe::create([
            'category_id' => Category::where('slug', 'pasta')->first()->id,
            'name' => 'Pasta a la Boloñesa en Una Olla',
            'slug' => 'pasta-bolonesa-una-olla',
            'description' => 'Pasta cubierta con salsa boloñesa de carne, todo cocinado en una sola olla. La pasta absorbe el sabor de la salsa mientras se cocina.',
            'objective' => 'Preparar una pasta con salsa boloñesa donde la pasta se cocina directamente en la salsa, absorbiendo los sabores de la carne y el jitomate.',
            'prep_time' => 10,
            'cook_time' => 20,
            'total_time' => 30,
            'servings' => 4,
            'difficulty' => 2,
            'cost' => 'Medio',
            'pressure_cook_time' => 5,
            'pressure_release' => 'rapida',
            'pressure_release_time' => 0,
            'saute_time' => 8,
            'chef_notes' => 'El tiempo de cocción de la pasta a presión es la mitad del tiempo indicado en el paquete, redondeando hacia abajo. Para pasta con tiempo de 10 minutos, cocinar 5 a presión.',
            'is_published' => true,
        ]);
        $recipe->tags()->attach(Tag::whereIn('slug', ['rapido', 'una-olla', 'alto-en-proteina'])->pluck('id'));

        $steps = [
            ['step_number' => 1, 'action' => 'Activar Sauté. Dorar 500g de carne molida de res, deshaciendo los grumos.', 'technical_fundament' => 'El sellado de la carne molida desarrolla sabor a través de la reacción de Maillard. Deshacer los grumos asegura cocción uniforme.', 'what_to_observe' => 'Carne dorada, no gris.', 'common_errors' => 'Mover la carne constantemente impide que se dore. Dejar reposar 2-3 minutos entre movimientos.'],
            ['step_number' => 2, 'action' => 'Agregar cebolla y ajo. Sofreír 2 minutos. Incorporar puré de jitomate y especias.', 'technical_fundament' => 'Sofreír los aromáticos desarrolla dulzor. El jitomate aporta acidez y umami.', 'what_to_observe' => 'Mezcla homogénea con aroma fragante.', 'common_errors' => 'Agregar la pasta antes del líquido puede causar Burn.'],
            ['step_number' => 3, 'action' => 'Agregar 3 tazas de caldo de pollo. Colocar 250g de pasta distribuyéndola uniformemente. NO revolver.', 'technical_fundament' => 'La pasta debe quedar sumergida para cocerse uniformemente. No revolver evita que se pegue al fondo.', 'what_to_observe' => 'Toda la pasta cubierta por líquido.', 'common_errors' => 'Revolver la pasta puede pegarla al fondo. Distribuir y presionar suavemente.'],
            ['step_number' => 4, 'action' => 'Cerrar tapa. Pressure Cook Alta por 5 minutos. Liberación Rápida.', 'technical_fundament' => '5 minutos corresponden a la mitad del tiempo de paquete (10 min pasta). Liberación rápida detiene la cocción para evitar sobrecocción.', 'what_to_observe' => 'Pasta al dente.', 'common_errors' => 'Tiempo excesivo produce pasta blanda. Siempre calcular mitad del tiempo del paquete.'],
        ];
        foreach ($steps as $step) {
            RecipeStep::create(array_merge(['recipe_id' => $recipe->id], $step));
        }
    }
}
