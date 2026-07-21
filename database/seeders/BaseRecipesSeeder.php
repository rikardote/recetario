<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Concept;
use App\Models\Equipment;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeAdaptation;
use App\Models\RecipeConcept;
use App\Models\RecipeError;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\RecipeVariant;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BaseRecipesSeeder extends Seeder
{
    private array $ingredients = [];
    private array $equipment = [];
    private array $tags = [];

    public function run(): void
    {
        $this->seedIngredients();
        $this->seedEquipment();
        $this->seedTags();

        $this->seedChoripollo();
        $this->seedCochinitaPibil();
        $this->seedPolloBBQ();
    }

    private function ing(string $name): int
    {
        if (!isset($this->ingredients[$name])) {
            $ing = Ingredient::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
            $this->ingredients[$name] = $ing->id;
        }
        return $this->ingredients[$name];
    }

    private function eq(string $name): int
    {
        if (!isset($this->equipment[$name])) {
            $eq = Equipment::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $name . ' para preparación.']
            );
            $this->equipment[$name] = $eq->id;
        }
        return $this->equipment[$name];
    }

    private function tag(string $name): int
    {
        if (!isset($this->tags[$name])) {
            $t = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
            $this->tags[$name] = $t->id;
        }
        return $this->tags[$name];
    }

    private function seedIngredients(): void
    {
        $names = [
            'Chorizo mexicano', 'Queso Oaxaca', 'Cilantro', 'Jitomate Roma',
            'Espaldilla de cerdo', 'Pasta de achiote', 'Jugo de naranja', 'Limón',
            'Vinagre', 'Comino', 'Orégano', 'Laurel', 'Hoja de plátano',
            'Catsup', 'Coca-Cola', 'Salsa inglesa', 'Mostaza', 'Azúcar',
            'Cebolla en polvo', 'Paprika', 'Consomé de pollo',
            'Champiñones', 'Queso crema', 'Crema', 'Rajas de chile poblano',
            'Pechuga de pollo', 'Muslo de pollo',
        ];
        foreach ($names as $n) {
            $this->ing($n);
        }
    }

    private function seedEquipment(): void
    {
        $names = ['Tabla', 'Cuchillo'];
        foreach ($names as $n) {
            $this->eq($n);
        }
        // Licuadora ya existe
        $this->eq('Licuadora');
    }

    private function seedTags(): void
    {
        $names = ['Tradicional', 'Mexicano', 'Una olla', 'Fácil'];
        foreach ($names as $n) {
            $this->tag($n);
        }
    }

    // ================================================================
    // CHORIPOLLO
    // ================================================================
    private function seedChoripollo(): void
    {
        $recipe = Recipe::create([
            'category_id' => Category::where('slug', 'pollo')->first()->id,
            'name' => 'Choripollo',
            'slug' => 'choripollo',
            'description' => 'Pollo jugoso acompañado de chorizo mexicano y queso Oaxaca derretido, con una salsa ligera de jitomate. Ideal para acompañar con arroz y tortillas.',
            'objective' => 'Preparar un pollo jugoso acompañado de chorizo mexicano y queso derretido, con una salsa ligera de jitomate ideal para acompañar con arroz y tortillas.',
            'prep_time' => 10,
            'cook_time' => 6,
            'total_time' => 25,
            'servings' => 4,
            'difficulty' => 2,
            'cost' => 'Medio',
            'result_texture' => 'Pollo muy jugoso. Chorizo dorado. Queso completamente derretido.',
            'result_consistency' => 'Salsa ligera que cubre la proteína sin ser pesada.',
            'result_flavor' => 'Equilibrio entre el chorizo condimentado, el pollo suave y el queso fundido.',
            'storage_refrigeration' => 'Refrigerar hasta 4 días en recipiente hermético.',
            'storage_freezing' => 'Congelar hasta 3 meses.',
            'pressure_cook_time' => 6,
            'pressure_release' => 'rapida',
            'saute_time' => 8,
            'chef_notes' => 'El chorizo libera grasa suficiente para todo el sofrito, no se requiere aceite adicional. Agregar el queso después de la presión usando solo el calor residual evita que se vuelva gomoso.',
            'is_published' => true,
        ]);

        // Tags
        $recipe->tags()->sync([
            $this->tag('Fácil'), $this->tag('Una olla'), $this->tag('Mexicano'),
            $this->tag('Alto en proteína'), $this->tag('Rápido'),
        ]);

        // Equipment
        $recipe->equipment()->sync([
            $this->eq('Instant Pot'), $this->eq('Tabla'), $this->eq('Cuchillo'), $this->eq('Licuadora'),
        ]);

        // Ingredients grouped by category
        $ingData = [
            ['name' => 'Pechuga de pollo', 'category' => 'proteinas', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'En cubos'],
            ['name' => 'Chorizo mexicano', 'category' => 'proteinas', 'quantity' => 250, 'unit' => 'g'],
            ['name' => 'Cebolla', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza'],
            ['name' => 'Ajo', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'dientes'],
            ['name' => 'Jitomate Roma', 'category' => 'verduras', 'quantity' => 2, 'unit' => 'piezas'],
            ['name' => 'Caldo de pollo', 'category' => 'liquidos', 'quantity' => 0.5, 'unit' => 'taza', 'is_optional' => false, 'notes' => 'Alternativa: 1/2 taza agua + 1/2 cdita consomé de pollo'],
            ['name' => 'Paprika', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Orégano', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Sal', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Pimienta negra', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Queso Oaxaca', 'category' => 'terminacion', 'quantity' => 250, 'unit' => 'g'],
            ['name' => 'Cilantro', 'category' => 'terminacion', 'is_optional' => true, 'notes' => 'Para decorar'],
        ];
        foreach ($ingData as $d) {
            $isOpt = $d['is_optional'] ?? false;
            $isRec = $d['is_recommended'] ?? !$isOpt;
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'ingredient_id' => $this->ing($d['name']),
                'category' => $d['category'],
                'quantity' => $d['quantity'] ?? null,
                'unit' => $d['unit'] ?? null,
                'is_recommended' => $isRec,
                'is_optional' => $isOpt,
                'notes' => $d['notes'] ?? null,
            ]);
        }

        // Steps
        $steps = [
            [
                'step_number' => 1,
                'action' => 'Activar Sauté. Dorar el chorizo mexicano desmenuzado.',
                'technical_fundament' => 'El chorizo libera grasa natural que sustituye el aceite. Esta grasa condimentada servirá como base para todo el sofrito posterior, impregnando cada ingrediente con su sabor característico.',
                'what_to_observe' => 'Chorizo ligeramente dorado, no quemado.',
                'common_errors' => 'Cocinar demasiado el chorizo hasta resecarlo. Solo debe soltar su grasa y dorarse ligeramente.',
            ],
            [
                'step_number' => 2,
                'action' => 'Agregar cebolla picada y ajo. Sofreír hasta que la cebolla esté translúcida.',
                'technical_fundament' => 'El sofrito en la grasa del chorizo desarrolla el dulzor natural de la cebolla mientras absorbe los condimentos del embutido.',
                'what_to_observe' => 'Cebolla translúcida con aroma fragante.',
                'common_errors' => null,
            ],
            [
                'step_number' => 3,
                'action' => 'Agregar el pollo en cubos. Sellar durante 2 minutos, moviendo ocasionalmente.',
                'technical_fundament' => 'El sellado rápido mejora la textura superficial de la carne sin buscar cocinarla completamente. La cocción total ocurrirá durante la presión.',
                'what_to_observe' => 'Exterior del pollo ligeramente blanqueado, interior aún crudo.',
                'common_errors' => 'Sobrecocinar el pollo en esta etapa. Solo es un sellado superficial.',
            ],
            [
                'step_number' => 4,
                'action' => 'Licuar jitomate, caldo de pollo, paprika, orégano y pimienta. Verter la salsa sobre el pollo.',
                'technical_fundament' => 'Licuar produce una salsa homogénea que se distribuye uniformemente durante la cocción a presión. Los condimentos se integran mejor que si se agregaran directamente.',
                'what_to_observe' => 'Salsa de color rojo uniforme cubriendo el pollo.',
                'common_errors' => 'No licuar resulta en una salsa grumosa con distribución desigual de condimentos.',
            ],
            [
                'step_number' => 5,
                'action' => 'Cerrar la tapa. Válvula en "Sealing". Programar Pressure Cook en Alta presión por 6 minutos. Liberación rápida.',
                'technical_fundament' => '6 minutos son suficientes para cocinar cubos de pechuga. La liberación rápida detiene la cocción inmediatamente, evitando que la pechuga —un corte magro— se sobrecocine.',
                'what_to_observe' => 'Al abrir, el pollo debe estar completamente cocido y la salsa burbujeante.',
                'common_errors' => 'Usar liberación natural con pechuga la reseca. La liberación rápida es la correcta para cortes magros.',
            ],
            [
                'step_number' => 6,
                'action' => 'Agregar el queso Oaxaca desmenuzado sobre la superficie. Cerrar la tapa (sin presión). Esperar 3 minutos.',
                'technical_fundament' => 'El calor residual de la olla y los alimentos derrite el queso de manera uniforme sin someterlo a presión, lo que evita que se vuelva gomoso o se separe la grasa.',
                'what_to_observe' => 'Queso completamente fundido e hilante.',
                'common_errors' => 'Agregar el queso antes de la cocción a presión lo vuelve duro y gomoso. Siempre después de la presión, usando solo calor residual.',
            ],
        ];
        foreach ($steps as $s) {
            RecipeStep::create(array_merge(['recipe_id' => $recipe->id], $s));
        }

        // Variants
        $variants = [
            ['name' => 'Con rajas', 'description' => 'Agregar rajas de chile poblano asadas y peladas.', 'ingredients_changes' => 'Agregar 2 chiles poblanos asados en rajas.', 'procedure_changes' => 'Incorporar las rajas junto con la cebolla en el paso 2.'],
            ['name' => 'Con champiñones', 'description' => 'Añadir champiñones para mayor volumen y textura.', 'ingredients_changes' => 'Agregar 200g de champiñones rebanados.', 'procedure_changes' => 'Saltear los champiñones después del chorizo.'],
            ['name' => 'Con crema', 'description' => 'Versión más cremosa y suave.', 'ingredients_changes' => 'Agregar 1/2 taza de crema ácida.', 'procedure_changes' => 'Incorporar la crema junto con el queso en el paso 6.'],
            ['name' => 'Con queso crema', 'description' => 'Sustituir el queso Oaxaca por queso crema para una versión más untuosa.', 'ingredients_changes' => 'Sustituir por 200g de queso crema en cubos.', 'procedure_changes' => 'Agregar el queso crema en el paso 6, mismo procedimiento.'],
        ];
        foreach ($variants as $v) {
            RecipeVariant::create(array_merge(['recipe_id' => $recipe->id], $v));
        }

        // Adaptations
        $adaptations = [
            ['scenario' => '¿Cómo adaptar si uso pollo congelado?', 'adaptation_text' => 'Omitir el sellado del paso 3. Colocar el pollo congelado directamente en la salsa. Aumentar Pressure Cook a 10 minutos.'],
            ['scenario' => '¿Cómo adaptar para Instant Pot 8 Qt?', 'adaptation_text' => 'No requiere modificaciones. Los tiempos se mantienen igual.'],
            ['scenario' => '¿Cómo hacer media receta?', 'adaptation_text' => 'Reducir ingredientes a la mitad. Mantener el mismo tiempo de cocción.'],
            ['scenario' => '¿Cómo duplicar la receta?', 'adaptation_text' => 'Duplicar todos los ingredientes. Mismo tiempo de cocción. No llenar más de 2/3 de la olla.'],
        ];
        foreach ($adaptations as $a) {
            RecipeAdaptation::create(array_merge(['recipe_id' => $recipe->id], $a));
        }

        // Concepts
        $conceptTexts = ['Sellado', 'Reducción sencilla', 'Uso del calor residual', 'Cocción por presión', 'Uso correcto del queso'];
        foreach ($conceptTexts as $c) {
            RecipeConcept::create(['recipe_id' => $recipe->id, 'concept_text' => $c]);
        }

        // Errors
        $errors = [
            ['problem' => 'El queso quedó duro', 'possible_cause' => 'Fue agregado antes de la cocción a presión.', 'solution' => 'Agregar el queso siempre después de la presión, usando solo calor residual.'],
            ['problem' => 'La salsa quedó muy líquida', 'possible_cause' => 'Exceso de líquido en la salsa.', 'solution' => 'Reducir en Sauté durante 5-8 minutos con la tapa abierta hasta obtener la consistencia deseada.'],
        ];
        foreach ($errors as $e) {
            RecipeError::create(array_merge(['recipe_id' => $recipe->id], $e));
        }
    }

    // ================================================================
    // COCHINITA PIBIL
    // ================================================================
    private function seedCochinitaPibil(): void
    {
        $recipe = Recipe::create([
            'category_id' => Category::where('slug', 'cerdo')->first()->id,
            'name' => 'Cochinita Pibil',
            'slug' => 'cochinita-pibil',
            'description' => 'Carne de cerdo extremadamente suave y fácil de deshebrar, marinada en achiote y cítricos, con los sabores tradicionales de la cocina yucateca.',
            'objective' => 'Obtener carne extremadamente suave y fácil de deshebrar con sabores tradicionales de achiote y cítricos, usando la cocción a presión para reducir drásticamente el tiempo de preparación.',
            'prep_time' => 20,
            'cook_time' => 45,
            'total_time' => 110,
            'servings' => 8,
            'difficulty' => 3,
            'cost' => 'Medio',
            'result_texture' => 'Carne que se deshebra con facilidad, fibras perfectamente separadas.',
            'result_color' => 'Rojo intenso característico del achiote.',
            'result_consistency' => 'Salsa espesa que se impregna en cada hebra de carne.',
            'result_flavor' => 'Cítrico, terroso y ligeramente ácido. Notas de achiote, naranja y especias.',
            'storage_refrigeration' => 'Refrigerar hasta 4 días en recipiente hermético.',
            'storage_freezing' => 'Congelar hasta 3 meses en porciones.',
            'pressure_cook_time' => 45,
            'pressure_release' => 'natural',
            'pressure_release_time' => 15,
            'saute_time' => 5,
            'chef_notes' => 'La espaldilla de cerdo contiene suficiente colágeno que, al cocinarse a presión durante 45 minutos, se convierte en gelatina produciendo una textura increíblemente suave. El marinado mínimo de 30 minutos es esencial; idealmente marinar 8-24 horas. No se requiere agregar agua adicional: el marinado genera suficiente vapor para alcanzar presión.',
            'is_published' => true,
        ]);

        $recipe->tags()->sync([
            $this->tag('Mexicano'), $this->tag('Tradicional'), $this->tag('Alto en proteína'),
            $this->tag('Meal Prep'), $this->tag('Sin Gluten'),
        ]);

        $recipe->equipment()->sync([
            $this->eq('Instant Pot'), $this->eq('Licuadora'),
        ]);

        $ingData = [
            ['name' => 'Espaldilla de cerdo', 'category' => 'proteinas', 'quantity' => 1.5, 'unit' => 'kg'],
            ['name' => 'Pasta de achiote', 'category' => 'condimentos', 'notes' => 'Base del marinado'],
            ['name' => 'Jugo de naranja', 'category' => 'liquidos', 'quantity' => 1, 'unit' => 'taza'],
            ['name' => 'Limón', 'category' => 'liquidos', 'quantity' => 2, 'unit' => 'piezas', 'notes' => 'Jugo'],
            ['name' => 'Vinagre', 'category' => 'liquidos', 'quantity' => 2, 'unit' => 'cucharadas'],
            ['name' => 'Ajo', 'category' => 'condimentos', 'quantity' => 4, 'unit' => 'dientes'],
            ['name' => 'Comino', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Orégano', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Sal', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Pimienta negra', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Laurel', 'category' => 'condimentos', 'is_optional' => true, 'notes' => '2 hojas'],
            ['name' => 'Hoja de plátano', 'category' => 'terminacion', 'is_optional' => true, 'notes' => 'Para aromatizar'],
        ];
        foreach ($ingData as $d) {
            $isOpt = $d['is_optional'] ?? false;
            $isRec = $d['is_recommended'] ?? !$isOpt;
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'ingredient_id' => $this->ing($d['name']),
                'category' => $d['category'],
                'quantity' => $d['quantity'] ?? null,
                'unit' => $d['unit'] ?? null,
                'is_recommended' => $isRec,
                'is_optional' => $isOpt,
                'notes' => $d['notes'] ?? null,
            ]);
        }

        $steps = [
            [
                'step_number' => 1,
                'action' => 'Licuar la pasta de achiote, jugo de naranja, jugo de limón, vinagre, ajo, comino, orégano, sal y pimienta hasta obtener un marinado homogéneo.',
                'technical_fundament' => 'Licuar permite distribuir uniformemente el achiote y las especias en el líquido. El medio ácido (cítricos y vinagre) inicia el proceso de desnaturalización de proteínas, ablandando la carne y permitiendo que los sabores penetren profundamente.',
                'what_to_observe' => 'Marinado de color rojo intenso, sin grumos de achiote.',
                'common_errors' => 'No licuar lo suficiente deja grumos de achiote que no se distribuyen uniformemente.',
            ],
            [
                'step_number' => 2,
                'action' => 'Cubrir completamente la carne de cerdo con el marinado. Refrigerar mínimo 30 minutos (ideal 8-24 horas).',
                'technical_fundament' => 'El tiempo de marinado es directamente proporcional a la profundidad del sabor. Los ácidos cítricos ablandan las fibras musculares mientras las especias penetran. Mínimo 30 minutos por seguridad; el resultado óptimo requiere varias horas.',
                'what_to_observe' => 'Carne completamente cubierta. El color del marinado debe ser uniforme sobre toda la superficie.',
                'common_errors' => 'Marinar por menos de 30 minutos: el sabor queda superficial. No refrigerar durante el marinado representa un riesgo sanitario.',
            ],
            [
                'step_number' => 3,
                'action' => 'Colocar la carne con TODO el marinado en la Instant Pot. Si se desea, forrar con hojas de plátano. No agregar agua adicional.',
                'technical_fundament' => 'El marinado contiene suficiente líquido (jugo de naranja, limón, vinagre) para generar el vapor necesario para alcanzar presión. Agregar agua diluiría los sabores concentrados del marinado. Las hojas de plátano aportan un aroma herbal distintivo.',
                'what_to_observe' => 'La carne debe estar en contacto con el líquido del marinado en el fondo de la olla.',
                'common_errors' => 'Agregar agua innecesaria diluye el sabor y prolonga la reducción posterior.',
            ],
            [
                'step_number' => 4,
                'action' => 'Cerrar la tapa. Válvula en "Sealing". Programar Pressure Cook en Alta presión por 45 minutos.',
                'technical_fundament' => 'La espaldilla de cerdo contiene abundante colágeno que requiere 45 minutos a alta presión para convertirse completamente en gelatina. Este tiempo garantiza que las fibras de colágeno se hidrolicen, produciendo una textura que se deshebra con facilidad.',
                'what_to_observe' => 'La olla debe alcanzar presión y comenzar la cuenta regresiva.',
                'common_errors' => 'Reducir el tiempo por debajo de 40 minutos produce carne dura difícil de deshebrar.',
            ],
            [
                'step_number' => 5,
                'action' => 'Al terminar, permitir Liberación Natural durante 15 minutos. Luego liberar el vapor restante.',
                'technical_fundament' => 'La liberación natural es obligatoria para cortes con alto colágeno. Durante este tiempo, la temperatura desciende gradualmente, permitiendo que las fibras musculares se relajen y reabsorban los jugos. Una liberación rápida expulsaría los jugos internos, resultando en carne seca.',
                'what_to_observe' => 'El pin flotante debe bajar por sí solo. Al abrir, la carne debe estar nadando en sus jugos.',
                'common_errors' => 'Liberación rápida: la carne queda seca y fibrosa. La espera de 15 minutos no es opcional.',
            ],
            [
                'step_number' => 6,
                'action' => 'Retirar la carne y deshebrar con dos tenedores. Regresar la carne deshebrada a la olla y mezclar con la salsa.',
                'technical_fundament' => 'Deshebrar en caliente es más fácil porque las fibras están relajadas. Mezclar nuevamente con la salsa asegura que cada hebra se impregne del sabor del marinado ya cocido.',
                'what_to_observe' => 'Carne que se separa en hebras con mínima resistencia.',
                'common_errors' => 'Deshebrar en frío requiere más esfuerzo y la carne no absorbe la salsa.',
            ],
            [
                'step_number' => 7,
                'action' => '(Opcional) Activar Sauté y reducir la salsa durante 5 minutos hasta que espese ligeramente.',
                'technical_fundament' => 'La reducción concentra los sabores del marinado cocido evaporando el exceso de agua. La salsa pasará de ser líquida a tener cuerpo, adhiriéndose mejor a la carne deshebrada.',
                'what_to_observe' => 'Salsa que cubre el dorso de una cuchara sin escurrir inmediatamente.',
                'common_errors' => 'Reducir en exceso concentra demasiado la sal y las especias.',
            ],
        ];
        foreach ($steps as $s) {
            RecipeStep::create(array_merge(['recipe_id' => $recipe->id], $s));
        }

        $variants = [
            ['name' => 'Con hojas de plátano', 'description' => 'Versión más aromática forrando la olla con hojas de plátano.', 'ingredients_changes' => 'Agregar 2 hojas de plátano.', 'procedure_changes' => 'Forrar el fondo de la olla con las hojas antes de colocar la carne.'],
            ['name' => 'Más picante', 'description' => 'Agregar chile habanero al marinado.', 'ingredients_changes' => 'Agregar 2 chiles habaneros sin semillas al licuado.', 'procedure_changes' => 'Licuar los habaneros junto con el marinado en el paso 1.'],
            ['name' => 'Terminada en horno', 'description' => 'Después de deshebrar, gratinar para obtener puntas crujientes.', 'ingredients_changes' => 'Sin cambios.', 'procedure_changes' => 'Después del paso 6, extender la carne en una charola y hornear a 200°C por 10 minutos.'],
        ];
        foreach ($variants as $v) {
            RecipeVariant::create(array_merge(['recipe_id' => $recipe->id], $v));
        }

        $adaptations = [
            ['scenario' => '¿Puedo usar carne congelada?', 'adaptation_text' => 'No recomendado. La carne congelada no absorbe el marinado. Descongelar completamente antes de marinar.'],
            ['scenario' => '¿Cómo adaptar para Instant Pot 8 Qt?', 'adaptation_text' => 'Sin cambios. Los tiempos y cantidades se mantienen.'],
            ['scenario' => '¿Cómo duplicar la receta?', 'adaptation_text' => 'Duplicar todos los ingredientes. El tiempo de cocción se mantiene igual. Verificar no exceder la capacidad máxima de la olla.'],
        ];
        foreach ($adaptations as $a) {
            RecipeAdaptation::create(array_merge(['recipe_id' => $recipe->id], $a));
        }

        $conceptTexts = ['Marinado', 'Colágeno', 'Deshebrado', 'Liberación natural', 'Reducción'];
        foreach ($conceptTexts as $c) {
            RecipeConcept::create(['recipe_id' => $recipe->id, 'concept_text' => $c]);
        }

        RecipeError::create([
            'recipe_id' => $recipe->id,
            'problem' => 'Carne dura que no se deshebra',
            'possible_cause' => 'Tiempo de presión insuficiente para convertir el colágeno.',
            'solution' => 'Cerrar nuevamente la tapa y cocinar 10 minutos adicionales a alta presión con liberación natural.',
        ]);
    }

    // ================================================================
    // POLLO BBQ CASERO CON PAPAS Y ZANAHORIAS
    // ================================================================
    private function seedPolloBBQ(): void
    {
        $recipe = Recipe::create([
            'category_id' => Category::where('slug', 'pollo')->first()->id,
            'name' => 'Pollo BBQ Casero con Papas y Zanahorias',
            'slug' => 'pollo-bbq-casero-papas-zanahorias',
            'description' => 'Muslos y piernas de pollo jugosos acompañados de papas y zanahorias, bañados en una salsa BBQ casera elaborada con catsup y Coca-Cola.',
            'objective' => 'Preparar un pollo muy jugoso acompañado de papas y zanahorias utilizando una salsa BBQ casera elaborada con catsup y Coca-Cola, logrando una salsa espesa y brillante.',
            'prep_time' => 15,
            'cook_time' => 12,
            'total_time' => 45,
            'servings' => 6,
            'difficulty' => 1,
            'cost' => 'Económico',
            'result_texture' => 'Pollo muy jugoso. Papas suaves pero enteras. Zanahorias tiernas.',
            'result_consistency' => 'Salsa BBQ espesa y brillante que se adhiere a la carne.',
            'result_flavor' => 'Dulce, ácido y ligeramente ahumado. La Coca-Cola aporta dulzor caramelizado.',
            'storage_refrigeration' => 'Refrigerar hasta 4 días.',
            'storage_freezing' => 'Congelar hasta 3 meses.',
            'pressure_cook_time' => 12,
            'pressure_release' => 'natural',
            'pressure_release_time' => 10,
            'saute_time' => 8,
            'chef_notes' => 'La Coca-Cola en la salsa BBQ no es un mito: su azúcar se carameliza durante la reducción y su acidez ayuda a balancear los sabores. Las verduras deben cortarse en trozos grandes y uniformes para que resistan la presión sin deshacerse.',
            'is_published' => true,
        ]);

        $recipe->tags()->sync([
            $this->tag('Fácil'), $this->tag('Una olla'), $this->tag('Económico'), $this->tag('Alto en proteína'),
        ]);

        $recipe->equipment()->sync([
            $this->eq('Instant Pot'),
        ]);

        $ingData = [
            ['name' => 'Muslo de pollo', 'category' => 'proteinas', 'quantity' => 1.5, 'unit' => 'kg', 'notes' => 'Muslos y piernas'],
            ['name' => 'Papa', 'category' => 'verduras', 'quantity' => 4, 'unit' => 'piezas'],
            ['name' => 'Zanahoria', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'piezas'],
            ['name' => 'Catsup', 'category' => 'liquidos', 'quantity' => 1, 'unit' => 'taza'],
            ['name' => 'Coca-Cola', 'category' => 'liquidos', 'quantity' => 0.5, 'unit' => 'taza'],
            ['name' => 'Salsa inglesa', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'cucharadas'],
            ['name' => 'Mostaza', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharada'],
            ['name' => 'Azúcar', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'cucharadas'],
            ['name' => 'Paprika', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Ajo', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'dientes', 'notes' => 'En polvo o fresco picado'],
            ['name' => 'Cebolla en polvo', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Sal', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Pimienta negra', 'category' => 'condimentos', 'notes' => 'Al gusto'],
        ];
        foreach ($ingData as $d) {
            $isOpt = $d['is_optional'] ?? false;
            $isRec = $d['is_recommended'] ?? !$isOpt;
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'ingredient_id' => $this->ing($d['name']),
                'category' => $d['category'],
                'quantity' => $d['quantity'] ?? null,
                'unit' => $d['unit'] ?? null,
                'is_recommended' => $isRec,
                'is_optional' => $isOpt,
                'notes' => $d['notes'] ?? null,
            ]);
        }

        $steps = [
            [
                'step_number' => 1,
                'action' => 'Preparar la salsa BBQ mezclando catsup, Coca-Cola, salsa inglesa, mostaza, azúcar, paprika, ajo en polvo, cebolla en polvo, sal y pimienta. Reservar.',
                'technical_fundament' => 'La salsa BBQ casera usa la catsup como base (aporta cuerpo y acidez), la Coca-Cola como endulzante caramelizable, la salsa inglesa para umami y profundidad, y la mostaza como emulsionante y potenciador. Mezclar en frío asegura integración uniforme.',
                'what_to_observe' => 'Salsa homogénea de color rojo oscuro. Consistencia similar a la miel líquida.',
                'common_errors' => 'No mezclar bien: los condimentos secos pueden formar grumos durante la cocción.',
            ],
            [
                'step_number' => 2,
                'action' => 'Colocar el pollo en la Instant Pot. Verter la mitad de la salsa BBQ sobre el pollo.',
                'technical_fundament' => 'Colocar la salsa en dos etapas (mitad ahora, mitad sobre las verduras) asegura que todos los ingredientes reciban cobertura de sabor durante la cocción a presión.',
                'what_to_observe' => 'Pollo parcialmente cubierto por la salsa.',
                'common_errors' => 'Agregar toda la salsa al inicio: las verduras de la capa superior no recibirán sabor.',
            ],
            [
                'step_number' => 3,
                'action' => 'Agregar las papas cortadas en trozos grandes y uniformes. Agregar las zanahorias en bastones gruesos. Cubrir con el resto de la salsa BBQ.',
                'technical_fundament' => 'Las verduras resistentes (papas, zanahorias) soportan perfectamente la cocción a presión siempre que se corten en trozos grandes (mínimo 3 cm). Trozos muy pequeños se desharán. Colocarlas sobre el pollo —no debajo— evita que se peguen al fondo y se quemen.',
                'what_to_observe' => 'Verduras distribuidas uniformemente, cubiertas por la salsa. Trozos de tamaño similar.',
                'common_errors' => 'Cortar las verduras demasiado pequeñas: se desharán durante la presión. Colocarlas en el fondo: pueden quemarse.',
            ],
            [
                'step_number' => 4,
                'action' => 'Cerrar la tapa. Válvula en "Sealing". Programar Pressure Cook en Alta presión por 12 minutos.',
                'technical_fundament' => '12 minutos a alta presión son el punto exacto para cocinar muslos de pollo y verduras resistentes simultáneamente. El pollo queda jugoso, las papas suaves pero enteras, y las zanahorias tiernas.',
                'what_to_observe' => 'La olla debe sellar correctamente y comenzar la cuenta regresiva.',
                'common_errors' => 'Agregar agua innecesaria: la salsa contiene suficiente líquido. Demasiado líquido diluye la salsa.',
            ],
            [
                'step_number' => 5,
                'action' => 'Al terminar, permitir Liberación Natural durante 10 minutos. Luego liberar el vapor restante.',
                'technical_fundament' => 'La liberación natural de 10 minutos es el equilibrio ideal: suficiente para que el pollo se relaje y mantenga su jugosidad, pero no tanto que las verduras se sobrecocinen.',
                'what_to_observe' => 'Al abrir, el pollo debe verse brillante y las verduras deben estar suaves al insertar un tenedor.',
                'common_errors' => 'Liberación rápida inmediata: el pollo queda seco. Liberación natural muy prolongada: las verduras se deshacen.',
            ],
            [
                'step_number' => 6,
                'action' => 'Retirar el pollo y las verduras con cuidado. Reservar en un platón.',
                'technical_fundament' => 'Retirar los sólidos primero permite trabajar la salsa sin romper las verduras ya cocidas.',
                'what_to_observe' => 'Verduras intactas, pollo jugoso.',
                'common_errors' => 'Dejar los sólidos durante la reducción: se romperán al revolver.',
            ],
            [
                'step_number' => 7,
                'action' => 'Activar Sauté (Normal). Reducir la salsa durante 8 minutos, revolviendo ocasionalmente, hasta que esté espesa y brillante.',
                'technical_fundament' => 'La reducción en Sauté evapora el exceso de agua concentrando los azúcares de la Coca-Cola y la catsup. La caramelización de estos azúcares produce el brillo característico y el sabor profundo de una buena salsa BBQ. Revolver previene que los azúcares se quemen en el fondo.',
                'what_to_observe' => 'Salsa que cubre el dorso de una cuchara. Color más oscuro que al inicio. Brillante, no opaca.',
                'common_errors' => 'No revolver: los azúcares de la Coca-Cola y catsup se queman fácilmente. Reducir demasiado: la salsa se vuelve excesivamente salada.',
            ],
            [
                'step_number' => 8,
                'action' => 'Regresar el pollo y las verduras a la olla. Mezclar suavemente para cubrir con la salsa reducida. Dejar reposar 3 minutos.',
                'technical_fundament' => 'El reposo final permite que la salsa se adhiera a cada pieza mientras la temperatura se equilibra. Mezclar suavemente evita romper las verduras ya cocidas.',
                'what_to_observe' => 'Cada pieza debe estar brillante y cubierta de salsa.',
                'common_errors' => 'Mezclar agresivamente rompe las verduras. Servir inmediatamente sin reposo no permite que los sabores se asienten.',
            ],
        ];
        foreach ($steps as $s) {
            RecipeStep::create(array_merge(['recipe_id' => $recipe->id], $s));
        }

        $variants = [
            ['name' => 'Con miel', 'description' => 'Sustituir el azúcar por miel para un dulzor más natural.', 'ingredients_changes' => 'Sustituir azúcar por 3 cucharadas de miel.', 'procedure_changes' => 'Agregar la miel en el paso 1 junto con los demás ingredientes.'],
            ['name' => 'Con chipotle', 'description' => 'Agregar chipotle para una versión picante y ahumada.', 'ingredients_changes' => 'Agregar 2 chiles chipotles adobados picados.', 'procedure_changes' => 'Licuar los chipotles con la salsa en el paso 1.'],
            ['name' => 'Con bourbon', 'description' => 'Sustituir parte de la Coca-Cola por bourbon.', 'ingredients_changes' => '1/4 taza bourbon + 1/4 taza Coca-Cola.', 'procedure_changes' => 'Agregar el bourbon junto con los líquidos en el paso 1.'],
            ['name' => 'Con cebolla caramelizada', 'description' => 'Agregar cebolla caramelizada al final.', 'ingredients_changes' => '1 cebolla grande fileteada.', 'procedure_changes' => 'Caramelizar la cebolla en Sauté antes del paso 1. Reservar y agregar al final.'],
        ];
        foreach ($variants as $v) {
            RecipeVariant::create(array_merge(['recipe_id' => $recipe->id], $v));
        }

        $adaptations = [
            ['scenario' => '¿Cómo adaptar si uso pollo congelado?', 'adaptation_text' => 'Colocar el pollo congelado directamente. Aumentar Pressure Cook a 16 minutos. Omitir sellado.'],
            ['scenario' => '¿Cómo adaptar para Instant Pot 8 Qt?', 'adaptation_text' => 'Sin cambios. Mismos tiempos y cantidades.'],
            ['scenario' => '¿Cómo hacer media receta?', 'adaptation_text' => 'Reducir ingredientes a la mitad. Mismo tiempo de cocción.'],
            ['scenario' => '¿Cómo duplicar la receta?', 'adaptation_text' => 'Duplicar ingredientes. Mismo tiempo. No exceder 2/3 de capacidad.'],
        ];
        foreach ($adaptations as $a) {
            RecipeAdaptation::create(array_merge(['recipe_id' => $recipe->id], $a));
        }

        $conceptTexts = ['Cocción por capas', 'Reducción', 'Caramelización', 'Liberación natural', 'Verduras resistentes'];
        foreach ($conceptTexts as $c) {
            RecipeConcept::create(['recipe_id' => $recipe->id, 'concept_text' => $c]);
        }

        $errors = [
            ['problem' => 'Salsa muy líquida', 'possible_cause' => 'No se redujo suficiente en Sauté o se agregó agua innecesaria.', 'solution' => 'Reducir en Sauté de 5 a 8 minutos adicionales con la tapa abierta.'],
            ['problem' => 'Papas deshechas', 'possible_cause' => 'Se cortaron demasiado pequeñas o se colocaron en el fondo.', 'solution' => 'Cortar las papas en trozos de mínimo 3 cm. Siempre colocarlas sobre el pollo, no debajo.'],
            ['problem' => 'Salsa con sabor a quemado', 'possible_cause' => 'Los azúcares de la Coca-Cola se caramelizaron en exceso.', 'solution' => 'Reducir a temperatura Normal (no Más). Revolver constantemente durante la reducción.'],
        ];
        foreach ($errors as $e) {
            RecipeError::create(array_merge(['recipe_id' => $recipe->id], $e));
        }
    }
}
