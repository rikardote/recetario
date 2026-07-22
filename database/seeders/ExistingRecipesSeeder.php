<?php

namespace Database\Seeders;

use App\Models\Category;
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

class ExistingRecipesSeeder extends Seeder
{
    private array $ingredients = [];
    private array $equipment = [];
    private array $tags = [];
    private array $categories = [];

    public function run(): void
    {
        $this->seedCategories();
        $this->seedIngredients();
        $this->seedEquipment();
        $this->seedTags();

        $this->seedChoripollo();           // id 11
        $this->seedPolloBBQ();             // id 14
        $this->seedCochinitaPibil();       // id 15
        $this->seedBistecRanchero();       // id 18
        $this->seedCaldoDeRes();           // id 19
        $this->seedPicadilloConPapas();    // id 20
        $this->seedFrijolesDeLaOlla();     // id 21
        $this->seedCaldoDePapas();         // id 22
        $this->seedTostadasSopes();        // id 23
    }

    // ================================================================
    // HELPERS
    // ================================================================

    private function cat(string $slug): int
    {
        if (!isset($this->categories[$slug])) {
            $c = Category::firstOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst($slug), 'description' => '']
            );
            $this->categories[$slug] = $c->id;
        }
        return $this->categories[$slug];
    }

    private function ing(string $name, ?string $slug = null): int
    {
        $key = $slug ?? Str::slug($name);
        if (!isset($this->ingredients[$key])) {
            $ing = Ingredient::firstOrCreate(
                ['slug' => $key],
                ['name' => $name, 'description' => '']
            );
            $this->ingredients[$key] = $ing->id;
        }
        return $this->ingredients[$key];
    }

    private function eq(string $name): int
    {
        $slug = Str::slug($name);
        if (!isset($this->equipment[$slug])) {
            $eq = Equipment::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $name . ' para preparación.']
            );
            $this->equipment[$slug] = $eq->id;
        }
        return $this->equipment[$slug];
    }

    private function tag(string $name): int
    {
        $slug = Str::slug($name);
        if (!isset($this->tags[$slug])) {
            $t = Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
            $this->tags[$slug] = $t->id;
        }
        return $this->tags[$slug];
    }

    private function createRecipe(array $data): Recipe
    {
        return Recipe::firstOrCreate(
            ['slug' => $data['slug']],
            $data
        );
    }

    private function addIngredients(Recipe $recipe, array $items): void
    {
        foreach ($items as $d) {
            $isOpt = $d['is_optional'] ?? false;
            $isRec = $d['is_recommended'] ?? !$isOpt;
            RecipeIngredient::firstOrCreate(
                [
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $this->ing($d['name'], $d['slug'] ?? null),
                    'category' => $d['category'],
                ],
                [
                    'quantity' => $d['quantity'] ?? null,
                    'unit' => $d['unit'] ?? null,
                    'is_recommended' => $isRec,
                    'is_optional' => $isOpt,
                    'notes' => $d['notes'] ?? null,
                ]
            );
        }
    }

    private function addSteps(Recipe $recipe, array $steps): void
    {
        foreach ($steps as $s) {
            RecipeStep::firstOrCreate(
                [
                    'recipe_id' => $recipe->id,
                    'step_number' => $s['step_number'],
                ],
                [
                    'action' => $s['action'],
                    'technical_fundament' => $s['technical_fundament'] ?? null,
                    'what_to_observe' => $s['what_to_observe'] ?? null,
                    'common_errors' => $s['common_errors'] ?? null,
                ]
            );
        }
    }

    private function addVariants(Recipe $recipe, array $variants): void
    {
        foreach ($variants as $v) {
            RecipeVariant::firstOrCreate(
                [
                    'recipe_id' => $recipe->id,
                    'name' => $v['name'],
                ],
                [
                    'description' => $v['description'] ?? '',
                    'ingredients_changes' => $v['ingredients_changes'] ?? null,
                    'procedure_changes' => $v['procedure_changes'] ?? null,
                ]
            );
        }
    }

    private function addAdaptations(Recipe $recipe, array $adaptations): void
    {
        foreach ($adaptations as $a) {
            RecipeAdaptation::firstOrCreate(
                [
                    'recipe_id' => $recipe->id,
                    'scenario' => $a['scenario'],
                ],
                ['adaptation_text' => $a['adaptation_text']]
            );
        }
    }

    private function addConcepts(Recipe $recipe, array $texts): void
    {
        foreach ($texts as $text) {
            RecipeConcept::firstOrCreate(
                [
                    'recipe_id' => $recipe->id,
                    'concept_text' => $text,
                ]
            );
        }
    }

    private function addErrors(Recipe $recipe, array $errors): void
    {
        foreach ($errors as $e) {
            RecipeError::firstOrCreate(
                [
                    'recipe_id' => $recipe->id,
                    'problem' => $e['problem'],
                ],
                [
                    'possible_cause' => $e['possible_cause'] ?? null,
                    'solution' => $e['solution'] ?? null,
                ]
            );
        }
    }

    private function seedCategories(): void
    {
        $cats = [
            ['name' => 'Pollo', 'slug' => 'pollo', 'icon' => '🐔'],
            ['name' => 'Cerdo', 'slug' => 'cerdo', 'icon' => '🐖'],
            ['name' => 'Res', 'slug' => 'res', 'icon' => '🐄'],
            ['name' => 'Sopas', 'slug' => 'sopas', 'icon' => '🍜'],
            ['name' => 'Legumbres', 'slug' => 'legumbres', 'icon' => '🫘'],
            ['name' => 'Antojitos', 'slug' => 'antojitos', 'icon' => '🌮'],
            ['name' => 'Frijoles', 'slug' => 'frijoles', 'icon' => '🫘'],
        ];
        foreach ($cats as $c) {
            Category::firstOrCreate(['slug' => $c['slug']], $c);
        }
    }

    private function seedIngredients(): void
    {
        $names = [
            'Pechuga de pollo' => 'pechuga-de-pollo',
            'Muslo de pollo' => 'muslo-de-pollo',
            'Cebolla' => 'cebolla',
            'Ajo' => 'ajo',
            'Caldo de pollo' => 'caldo-de-pollo',
            'Papa' => 'papa',
            'Zanahoria' => 'zanahoria',
            'Sal' => 'sal',
            'Pimienta negra' => 'pimienta-negra',
            'Chorizo mexicano' => 'chorizo-mexicano',
            'Queso Oaxaca' => 'queso-oaxaca',
            'Cilantro' => 'cilantro',
            'Jitomate Roma' => 'jitomate-roma',
            'Espaldilla de cerdo' => 'espaldilla-de-cerdo',
            'Pasta de achiote' => 'pasta-de-achiote',
            'Jugo de naranja' => 'jugo-de-naranja',
            'Limón' => 'limon',
            'Vinagre' => 'vinagre',
            'Comino' => 'comino',
            'Orégano' => 'oregano',
            'Laurel' => 'laurel',
            'Hoja de plátano' => 'hoja-de-platano',
            'Catsup' => 'catsup',
            'Coca-Cola' => 'coca-cola',
            'Salsa inglesa' => 'salsa-inglesa',
            'Mostaza' => 'mostaza',
            'Azúcar' => 'azucar',
            'Cebolla en polvo' => 'cebolla-en-polvo',
            'Paprika' => 'paprika',
            'Pimienta' => 'pimienta',
            'Bistec de res' => 'bistec-de-res',
            'Chile jalapeño' => 'chile-jalapeno',
            'Caldo de res' => 'caldo-de-res',
            'Orégano seco' => 'oregano-seco',
            'Comino molido' => 'comino-molido',
            'Cilantro fresco' => 'cilantro-fresco',
            'Chambarete de res con hueso' => 'chambarete-de-res-con-hueso',
            'Zanahorias' => 'zanahorias',
            'Papas' => 'papas',
            'Elotes' => 'elotes',
            'Calabacitas' => 'calabacitas',
            'Chayote' => 'chayote',
            'Cebolla blanca' => 'cebolla-blanca',
            'Agua' => 'agua',
            'Limones' => 'limones',
            'Cebolla picada' => 'cebolla-picada',
            'Chile serrano' => 'chile-serrano',
            'Hojas de plátano' => 'hojas-de-platano',
            'Carne molida de res (90/10 o 85/15)' => 'carne-molida-de-res-9010-o-8515',
            'Frijol pinto o peruano' => 'frijol-pinto-o-peruano',
            'Papa blanca' => 'papa-blanca',
            'Caldo de pollo o verduras' => 'caldo-de-pollo-o-verduras',
            'Chambarete, diezmillo o espaldilla de res' => 'chambarete-diezmillo-o-espaldilla-de-res',
        ];
        foreach ($names as $name => $slug) {
            $this->ing($name, $slug);
        }
    }

    private function seedEquipment(): void
    {
        $names = [
            'Instant Pot Duo Plus', 'Instant Pot', 'Licuadora',
            'Tabla', 'Tabla para cortar', 'Cuchillo',
            'Pinzas', 'Cuchara de madera', 'Espumadera',
            'Espátula de madera', 'Dos tenedores',
        ];
        foreach ($names as $n) {
            $this->eq($n);
        }
    }

    private function seedTags(): void
    {
        $names = [
            'pollo', 'queso', 'chorizo', 'bbq', 'Fácil', 'papas',
            'economic', 'cerdo', 'achiote', 'yucateco', 'tradicion',
            'res', 'bistec', 'ranchero', 'comida-mexicana', 'instant-pot',
            'Una olla', 'sopa', 'verduras', 'comfort-food',
            'carne-molida', 'picadillo', 'comida-casera',
            'frijoles', 'básicos', 'Meal Prep', 'mexicanos',
            'papa', 'económica', 'vegetariana',
            'tostadas', 'sopes', 'antojitos', 'carne-deshebrada',
        ];
        foreach ($names as $n) {
            $this->tag($n);
        }
    }

    // ================================================================
    // 1. CHORIPOLLO (id=11)
    // ================================================================
    private function seedChoripollo(): void
    {
        $recipe = $this->createRecipe([
            'name' => 'Choripollo',
            'slug' => 'choripollo',
            'description' => 'Preparar un pollo jugoso con chorizo y queso Oaxaca utilizando una sola olla.',
            'objective' => 'Preparar un pollo jugoso con chorizo y queso Oaxaca utilizando una sola olla.',
            'prep_time' => 10,
            'cook_time' => 6,
            'total_time' => 25,
            'servings' => 4,
            'difficulty' => 2,
            'cost' => '$$',
            'result_texture' => 'Pollo muy jugoso.',
            'result_color' => 'Rojo intenso.',
            'pressure_cook_time' => 6,
            'pressure_release' => 'rapida',
            'saute_time' => 8,
            'chef_notes' => 'El queso Oaxaca nunca debe cocinarse bajo presión.',
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('pollo') => ['is_primary' => true],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('pollo'), $this->tag('queso'), $this->tag('chorizo'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot Duo Plus'),
            $this->eq('Tabla'),
            $this->eq('Cuchillo'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Pechuga de pollo', 'category' => 'proteinas', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'Cubos de 4 cm'],
            ['name' => 'Chorizo mexicano', 'category' => 'proteinas', 'quantity' => 250, 'unit' => 'g', 'notes' => 'Sin tripa'],
            ['name' => 'Cebolla', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza'],
            ['name' => 'Ajo', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'dientes'],
            ['name' => 'Caldo de pollo', 'category' => 'liquidos', 'quantity' => 0.5, 'unit' => 'taza'],
            ['name' => 'Sal', 'category' => 'condimentos'],
            ['name' => 'Pimienta', 'category' => 'condimentos'],
            ['name' => 'Queso Oaxaca', 'category' => 'terminacion', 'quantity' => 250, 'unit' => 'g', 'notes' => 'Deshebrado'],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => 'Dorar el chorizo.',
             'technical_fundament' => 'El chorizo libera grasa suficiente para cocinar sin aceite.',
             'what_to_observe' => 'Debe cambiar a un color ligeramente oscuro.',
             'common_errors' => 'Sobre cocinar el chorizo.'],
            ['step_number' => 2, 'action' => 'Agregar cebolla y ajo.',
             'technical_fundament' => 'Se desarrolla el dulzor natural.',
             'what_to_observe' => 'La cebolla debe quedar translúcida.',
             'common_errors' => 'Quemar el ajo.'],
            ['step_number' => 3, 'action' => 'Agregar el pollo. Sellar durante dos minutos.',
             'technical_fundament' => 'Mejora la textura.',
             'what_to_observe' => 'Debe perder el color rosado superficial.',
             'common_errors' => 'Intentar cocinar completamente el pollo.'],
        ]);

        $this->addConcepts($recipe, ['Sellado', 'Calor residual', 'Reducción']);
    }

    // ================================================================
    // 2. POLLO BBQ CASERO CON PAPAS Y ZANAHORIAS (id=14)
    // ================================================================
    private function seedPolloBBQ(): void
    {
        $recipe = $this->createRecipe([
            'name' => 'Pollo BBQ Casero con Papas y Zanahorias',
            'slug' => 'pollo-bbq-casero-papas-zanahorias',
            'description' => 'Preparar un pollo muy jugoso acompañado de papas y zanahorias utilizando una salsa BBQ casera elaborada con catsup y Coca-Cola, logrando una salsa espesa y brillante.',
            'objective' => 'Preparar un pollo muy jugoso acompañado de papas y zanahorias utilizando una salsa BBQ casera elaborada con catsup y Coca-Cola, logrando una salsa espesa y brillante.',
            'prep_time' => 15,
            'cook_time' => 12,
            'total_time' => 45,
            'servings' => 6,
            'difficulty' => 1,
            'cost' => '$',
            'result_texture' => 'Pollo muy jugoso. Papas suaves pero enteras. Zanahorias tiernas.',
            'result_color' => 'Rojo oscuro brillante.',
            'result_consistency' => 'Espesa y brillante, caramelizada.',
            'result_flavor' => 'Dulce, ahumado, BBQ.',
            'storage_refrigeration' => '4 días en recipiente hermético.',
            'storage_freezing' => '3 meses en porciones.',
            'pressure_cook_time' => 12,
            'pressure_release' => 'natural',
            'pressure_release_time' => 10,
            'saute_time' => 8,
            'chef_notes' => 'La Coca-Cola en la salsa BBQ no es un mito: su azúcar se carameliza durante la reducción y su acidez ayuda a balancear los sabores. Las verduras deben cortarse en trozos grandes y uniformes para que resistan la presión sin deshacerse.',
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('pollo') => ['is_primary' => true],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('pollo'), $this->tag('bbq'), $this->tag('Fácil'),
            $this->tag('papas'), $this->tag('economic'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Muslo de pollo', 'category' => 'proteinas', 'quantity' => 1.5, 'unit' => 'kg', 'notes' => 'Muslos y piernas'],
            ['name' => 'Papa', 'category' => 'verduras', 'quantity' => 4, 'unit' => 'piezas', 'notes' => 'Trozos grandes de 3 cm'],
            ['name' => 'Zanahoria', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'piezas', 'notes' => 'Bastones gruesos'],
            ['name' => 'Catsup', 'category' => 'liquidos', 'quantity' => 1, 'unit' => 'taza'],
            ['name' => 'Coca-Cola', 'category' => 'liquidos', 'quantity' => 0.5, 'unit' => 'taza'],
            ['name' => 'Salsa inglesa', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'cucharadas'],
            ['name' => 'Mostaza', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharada'],
            ['name' => 'Azúcar', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'cucharadas'],
            ['name' => 'Paprika', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cdita'],
            ['name' => 'Ajo', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'dientes', 'notes' => 'En polvo o fresco picado'],
            ['name' => 'Cebolla en polvo', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cdita'],
            ['name' => 'Sal', 'category' => 'condimentos'],
            ['name' => 'Pimienta', 'category' => 'condimentos'],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => 'Preparar la salsa BBQ mezclando catsup, Coca-Cola, salsa inglesa, mostaza, azúcar, paprika, ajo en polvo o fresco, cebolla en polvo, sal y pimienta. Reservar.',
             'technical_fundament' => 'La salsa BBQ casera usa la catsup como base que aporta cuerpo y acidez, la Coca-Cola como endulzante caramelizable, la salsa inglesa para umami y la mostaza como emulsionante. Mezclar en frío asegura integración uniforme.',
             'what_to_observe' => 'Salsa homogénea de color rojo oscuro, consistencia similar a miel líquida.',
             'common_errors' => 'No mezclar bien: los condimentos secos pueden formar grumos durante la cocción.'],
            ['step_number' => 2, 'action' => 'Colocar el pollo en la Instant Pot. Verter la mitad de la salsa BBQ sobre el pollo.',
             'technical_fundament' => 'Colocar la salsa en dos etapas asegura que todos los ingredientes reciban cobertura de sabor durante la cocción a presión.',
             'what_to_observe' => 'Pollo parcialmente cubierto por la salsa.',
             'common_errors' => 'Agregar toda la salsa al inicio: las verduras de la capa superior no recibirán sabor.'],
            ['step_number' => 3, 'action' => 'Agregar las papas cortadas en trozos grandes y uniformes. Agregar las zanahorias en bastones gruesos. Cubrir con el resto de la salsa BBQ.',
             'technical_fundament' => 'Las verduras resistentes como papas y zanahorias soportan perfectamente la cocción a presión siempre que se corten en trozos grandes de mínimo 3 cm. Colocarlas sobre el pollo evita que se peguen al fondo.',
             'what_to_observe' => 'Verduras distribuidas uniformemente, cubiertas por la salsa. Trozos de tamaño similar.',
             'common_errors' => 'Cortar las verduras demasiado pequeñas se desharán. Colocarlas en el fondo pueden quemarse.'],
            ['step_number' => 4, 'action' => 'Cerrar la tapa. Válvula en Sealing. Programar Pressure Cook en Alta presión por 12 minutos.',
             'technical_fundament' => '12 minutos a alta presión son el punto exacto para cocinar muslos de pollo y verduras simultáneamente. El pollo queda jugoso, las papas suaves pero enteras.',
             'what_to_observe' => 'La olla debe sellar correctamente y comenzar la cuenta regresiva.',
             'common_errors' => 'Agregar agua innecesaria: la salsa contiene suficiente líquido. Demasiado líquido diluye la salsa.'],
            ['step_number' => 5, 'action' => 'Al terminar, permitir Liberación Natural durante 10 minutos. Luego liberar el vapor restante.',
             'technical_fundament' => 'La liberación natural de 10 minutos es el equilibrio ideal: suficiente para que el pollo se relaje y mantenga su jugosidad, pero no tanto que las verduras se sobrecocinen.',
             'what_to_observe' => 'Al abrir, pollo brillante y verduras suaves al insertar un tenedor.',
             'common_errors' => 'Liberación rápida inmediata reseca el pollo. Liberación natural muy prolongada deshace las verduras.'],
            ['step_number' => 6, 'action' => 'Retirar el pollo y las verduras con cuidado. Reservar en un platón.',
             'technical_fundament' => 'Retirar los sólidos primero permite trabajar la salsa sin romper las verduras ya cocidas.',
             'what_to_observe' => 'Verduras intactas, pollo jugoso.',
             'common_errors' => 'Dejar los sólidos durante la reducción: se romperán al revolver.'],
            ['step_number' => 7, 'action' => 'Activar Sauté. Reducir la salsa durante 8 minutos, revolviendo ocasionalmente, hasta que esté espesa y brillante.',
             'technical_fundament' => 'La reducción evapora el exceso de agua concentrando los azúcares de la Coca-Cola y catsup. La caramelización de estos azúcares produce el brillo característico. Revolver previene que los azúcares se quemen en el fondo.',
             'what_to_observe' => 'Salsa que cubre el dorso de una cuchara. Color más oscuro que al inicio. Brillante.',
             'common_errors' => 'No revolver: los azúcares se queman fácilmente. Reducir demasiado: la salsa se vuelve excesivamente salada.'],
            ['step_number' => 8, 'action' => 'Regresar el pollo y las verduras a la olla. Mezclar suavemente para cubrir con la salsa reducida. Dejar reposar 3 minutos.',
             'technical_fundament' => 'El reposo final permite que la salsa se adhiera a cada pieza mientras la temperatura se equilibra. Mezclar suavemente evita romper las verduras.',
             'what_to_observe' => 'Cada pieza debe estar brillante y cubierta de salsa.',
             'common_errors' => 'Mezclar agresivamente rompe las verduras. Servir sin reposo no permite que los sabores se asienten.'],
        ]);

        $this->addVariants($recipe, [
            ['name' => 'Con miel', 'description' => 'Sustituir el azúcar por 3 cucharadas de miel.'],
            ['name' => 'Con chipotle', 'description' => 'Agregar 2 chiles chipotles adobados picados a la salsa.'],
            ['name' => 'Con bourbon', 'description' => 'Sustituir 1/4 taza de Coca-Cola por bourbon.'],
            ['name' => 'Con cebolla caramelizada', 'description' => 'Caramelizar 1 cebolla fileteada en Sauté y agregar al final.'],
        ]);

        $this->addAdaptations($recipe, [
            ['scenario' => 'Pollo congelado', 'adaptation_text' => 'Colocar el pollo congelado directamente. Aumentar Pressure Cook a 16 minutos.'],
            ['scenario' => 'Instant Pot 8Qt', 'adaptation_text' => 'Sin cambios. Mismos tiempos y cantidades.'],
            ['scenario' => 'Media receta', 'adaptation_text' => 'Reducir ingredientes a la mitad. Mismo tiempo de cocción.'],
        ]);

        $this->addConcepts($recipe, ['Cocción por capas', 'Reducción', 'Caramelización', 'Liberación natural', 'Verduras resistentes']);

        $this->addErrors($recipe, [
            ['problem' => 'Salsa muy líquida', 'possible_cause' => 'No se redujo suficiente en Sauté o se agregó agua innecesaria.', 'solution' => 'Reducir en Sauté de 5 a 8 minutos adicionales con la tapa abierta.'],
            ['problem' => 'Papas deshechas', 'possible_cause' => 'Se cortaron demasiado pequeñas o se colocaron en el fondo.', 'solution' => 'Cortar en trozos de mínimo 3 cm. Colocarlas sobre el pollo, no debajo.'],
            ['problem' => 'Salsa con sabor a quemado', 'possible_cause' => 'Los azúcares de la Coca-Cola se caramelizaron en exceso.', 'solution' => 'Reducir a temperatura Normal, no Más. Revolver constantemente durante la reducción.'],
        ]);
    }

    // ================================================================
    // 3. COCHINITA PIBIL (id=15)
    // ================================================================
    private function seedCochinitaPibil(): void
    {
        $recipe = $this->createRecipe([
            'name' => 'Cochinita Pibil',
            'slug' => 'cochinita-pibil',
            'description' => 'Obtener carne extremadamente suave y fácil de deshebrar con sabores tradicionales de achiote y cítricos, usando la cocción a presión para reducir drásticamente el tiempo de preparación.',
            'objective' => 'Obtener carne extremadamente suave y fácil de deshebrar con sabores tradicionales de achiote y cítricos, usando la cocción a presión para reducir drásticamente el tiempo de preparación.',
            'prep_time' => 20,
            'cook_time' => 45,
            'total_time' => 80,
            'servings' => 8,
            'difficulty' => 3,
            'cost' => '$$',
            'result_texture' => 'Carne que se deshebra con facilidad. Fibras perfectamente separadas.',
            'result_color' => 'Rojo intenso característico del achiote.',
            'result_consistency' => 'Espesa que se impregna en cada hebra.',
            'result_flavor' => 'Cítrico, terroso, achiote y especias.',
            'storage_refrigeration' => '4 días en recipiente hermético.',
            'storage_freezing' => '3 meses en porciones.',
            'pressure_cook_time' => 45,
            'pressure_release' => 'natural',
            'pressure_release_time' => 15,
            'saute_time' => 5,
            'chef_notes' => 'La espaldilla de cerdo contiene suficiente colágeno que, al cocinarse a presión, se convierte en gelatina produciendo una textura increíblemente suave. El marinado mínimo es 30 minutos; idealmente 8-24 horas. No se requiere agregar agua adicional.',
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('cerdo') => ['is_primary' => true],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('cerdo'), $this->tag('achiote'), $this->tag('yucateco'), $this->tag('tradicion'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot'),
            $this->eq('Licuadora'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Espaldilla de cerdo', 'category' => 'proteinas', 'quantity' => 1.5, 'unit' => 'kg', 'notes' => 'En trozos grandes'],
            ['name' => 'Pasta de achiote', 'category' => 'condimentos', 'quantity' => 4, 'unit' => 'cucharadas'],
            ['name' => 'Jugo de naranja', 'category' => 'liquidos', 'quantity' => 1, 'unit' => 'taza'],
            ['name' => 'Limón', 'category' => 'liquidos', 'quantity' => 2, 'unit' => 'piezas'],
            ['name' => 'Vinagre', 'category' => 'liquidos', 'quantity' => 2, 'unit' => 'cucharadas'],
            ['name' => 'Ajo', 'category' => 'condimentos', 'quantity' => 4, 'unit' => 'dientes'],
            ['name' => 'Comino', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cdita'],
            ['name' => 'Orégano', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cdita'],
            ['name' => 'Sal', 'category' => 'condimentos'],
            ['name' => 'Pimienta', 'category' => 'condimentos'],
            ['name' => 'Laurel', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'hojas'],
            ['name' => 'Hojas de plátano', 'category' => 'condimentos', 'is_optional' => true],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => 'Licuar la pasta de achiote, jugo de naranja, jugo de limón, vinagre, ajo, comino, orégano, sal y pimienta hasta obtener un marinado homogéneo.',
             'technical_fundament' => 'Licuar permite distribuir uniformemente el achiote y las especias en el líquido. El medio ácido de los cítricos ablanda la carne y permite que los sabores penetren profundamente.',
             'what_to_observe' => 'Marinado de color rojo intenso, sin grumos de achiote.',
             'common_errors' => 'No licuar lo suficiente deja grumos que no se distribuyen uniformemente.'],
            ['step_number' => 2, 'action' => 'Cubrir completamente la carne de cerdo con el marinado. Refrigerar mínimo 30 minutos (ideal 8-24 horas).',
             'technical_fundament' => 'El tiempo de marinado es directamente proporcional a la profundidad del sabor. Los ácidos cítricos ablandan las fibras musculares mientras las especias penetran.',
             'what_to_observe' => 'Carne completamente cubierta. Color uniforme del marinado.'],
            ['step_number' => 3, 'action' => 'Colocar la carne con todo el marinado en la Instant Pot. No agregar agua adicional.',
             'technical_fundament' => 'El marinado contiene suficiente líquido para generar vapor. Agregar agua diluiría los sabores concentrados.',
             'what_to_observe' => 'La carne debe estar en contacto con el líquido del marinado en el fondo.',
             'common_errors' => 'Agregar agua innecesaria diluye el sabor y prolonga la reducción posterior.'],
            ['step_number' => 4, 'action' => 'Cerrar la tapa. Válvula en Sealing. Programar Pressure Cook en Alta presión por 45 minutos.',
             'technical_fundament' => 'La espaldilla de cerdo contiene abundante colágeno que requiere 45 minutos a alta presión para convertirse completamente en gelatina.',
             'what_to_observe' => 'La olla debe alcanzar presión y comenzar la cuenta regresiva.',
             'common_errors' => 'Reducir el tiempo por debajo de 40 minutos produce carne dura difícil de deshebrar.'],
            ['step_number' => 5, 'action' => 'Al terminar, permitir Liberación Natural durante 15 minutos. Luego liberar el vapor restante.',
             'technical_fundament' => 'La liberación natural es obligatoria para cortes con alto colágeno. La temperatura desciende gradualmente permitiendo que las fibras musculares se relajen y reabsorban los jugos.',
             'what_to_observe' => 'El pin flotante debe bajar por sí solo. Al abrir la carne debe estar nadando en sus jugos.',
             'common_errors' => 'Liberación rápida: la carne queda seca y fibrosa.'],
            ['step_number' => 6, 'action' => 'Retirar la carne y deshebrar con dos tenedores. Regresar la carne deshebrada a la olla y mezclar con la salsa.',
             'technical_fundament' => 'Deshebrar en caliente es más fácil porque las fibras están relajadas. Mezclar con la salsa asegura que cada hebra se impregne del sabor.',
             'what_to_observe' => 'Carne que se separa en hebras con mínima resistencia.'],
            ['step_number' => 7, 'action' => 'Activar Sauté y reducir la salsa durante 5 minutos hasta que espese ligeramente.',
             'technical_fundament' => 'La reducción concentra los sabores del marinado cocido evaporando el exceso de agua.',
             'what_to_observe' => 'Salsa que cubre el dorso de una cuchara sin escurrir inmediatamente.',
             'common_errors' => 'Reducir en exceso concentra demasiado la sal.'],
        ]);

        $this->addVariants($recipe, [
            ['name' => 'Con hojas de plátano', 'description' => 'Forrar la olla con hojas de plátano para aroma tradicional.'],
            ['name' => 'Más picante', 'description' => 'Agregar 2 chiles habaneros sin semillas al licuado.'],
            ['name' => 'Terminada en horno', 'description' => 'Después de deshebrar, hornear a 200°C por 10 min para puntas crujientes.'],
        ]);

        $this->addAdaptations($recipe, [
            ['scenario' => 'Carne congelada', 'adaptation_text' => 'No recomendado. Descongelar antes de marinar.'],
            ['scenario' => 'Instant Pot 8Qt', 'adaptation_text' => 'Sin cambios.'],
            ['scenario' => 'Doble receta', 'adaptation_text' => 'Duplicar ingredientes. Mismo tiempo.'],
        ]);

        $this->addConcepts($recipe, ['Marinado', 'Colágeno', 'Deshebrado', 'Liberación natural', 'Reducción']);

        $this->addErrors($recipe, [
            ['problem' => 'Carne dura que no se deshebra', 'possible_cause' => 'Tiempo de presión insuficiente para convertir el colágeno.', 'solution' => 'Cerrar nuevamente la tapa y cocinar 10 minutos adicionales a alta presión con liberación natural.'],
        ]);
    }

    // ================================================================
    // 4. BISTEC RANCHERO (id=18)
    // ================================================================
    private function seedBistecRanchero(): void
    {
        $recipe = $this->createRecipe([
            'name' => 'Bistec Ranchero',
            'slug' => 'bistec-ranchero',
            'description' => 'Preparar bistec de res suave y jugoso cocinado en una salsa ranchera de jitomate, cebolla y chile, obteniendo una salsa ligeramente espesa ideal para acompañar con arroz, frijoles o tortillas recién hechas.',
            'objective' => 'Preparar bistec de res suave y jugoso cocinado en una salsa ranchera de jitomate, cebolla y chile, obteniendo una salsa ligeramente espesa ideal para acompañar con arroz, frijoles o tortillas recién hechas.',
            'prep_time' => 15,
            'cook_time' => 10,
            'total_time' => 35,
            'servings' => 4,
            'difficulty' => 2,
            'cost' => '$$',
            'pressure_cook_time' => 10,
            'pressure_release' => 'rapida',
            'saute_time' => 6,
            'chef_notes' => "- Los mejores cortes para Instant Pot son **diezmillo, aguayón, paleta y espaldilla**, ya que contienen tejido conectivo que se vuelve muy tierno con la cocción a presión.\n- Si utilizas cortes muy delgados para asar, reduce el tiempo de **Pressure Cook** a **6 u 8 minutos**.\n- Este platillo combina muy bien con **arroz rojo**, **frijoles de la olla**, **frijoles puercos** o **puré de papa**.",
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('res') => ['is_primary' => true],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('res'), $this->tag('bistec'), $this->tag('ranchero'),
            $this->tag('comida-mexicana'), $this->tag('instant-pot'), $this->tag('Una olla'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot Duo Plus'),
            $this->eq('Tabla para cortar'),
            $this->eq('Cuchillo'),
            $this->eq('Pinzas'),
            $this->eq('Cuchara de madera'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Bistec de res', 'category' => 'proteinas', 'quantity' => 1, 'unit' => 'kg', 'notes' => 'Cortado en tiras o cuadros grandes (2 cm de ancho)'],
            ['name' => 'Jitomate Roma', 'category' => 'verduras', 'quantity' => 4, 'unit' => 'piezas', 'notes' => 'Picado en cubos'],
            ['name' => 'Cebolla blanca', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza', 'notes' => 'Plumas gruesas'],
            ['name' => 'Ajo', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'dientes', 'notes' => 'Picado'],
            ['name' => 'Chile jalapeño', 'category' => 'verduras', 'quantity' => 2, 'unit' => 'piezas', 'notes' => 'Rodajas'],
            ['name' => 'Caldo de res', 'category' => 'liquidos', 'quantity' => 1, 'unit' => 'taza'],
            ['name' => 'Orégano seco', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Comino molido', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Pimienta negra', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Sal', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Cilantro fresco', 'category' => 'terminacion', 'notes' => 'Picado'],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => "Seleccionar **Sauté** (Nivel Alto).\n\nAgregar una cucharada de aceite.\n\nSellar el bistec durante 3 a 4 minutos.",
             'technical_fundament' => 'El sellado desarrolla sabores mediante la reacción de Maillard, aportando mayor profundidad al platillo.',
             'what_to_observe' => 'La carne debe cambiar de color y presentar zonas ligeramente doradas.',
             'common_errors' => 'Intentar cocinar completamente la carne durante el sellado.'],
            ['step_number' => 2, 'action' => "Agregar:\n\n- Cebolla.\n- Ajo.\n\nCocinar durante 2 minutos.\n\nPosteriormente incorporar el chile jalapeño.",
             'technical_fundament' => 'La cebolla libera azúcares naturales y el ajo aromatiza el aceite antes de añadir el jitomate.',
             'what_to_observe' => 'La cebolla debe verse ligeramente transparente.',
             'common_errors' => 'Quemar el ajo.'],
            ['step_number' => 3, 'action' => "Agregar:\n\n- Jitomate.\n- Caldo de res.\n- Orégano.\n- Comino.\n- Pimienta.\n- Sal.\n\nMezclar cuidadosamente.",
             'technical_fundament' => "El jitomate aportará suficiente líquido para formar una salsa durante la cocción a presión.\n\nEl caldo intensifica el sabor sin necesidad de utilizar grandes cantidades de líquido.",
             'what_to_observe' => 'El fondo de la olla debe quedar completamente desglasado.',
             'common_errors' => 'No despegar los residuos del fondo después del sellado, lo que puede provocar el aviso **Burn**.'],
            ['step_number' => 4, 'action' => "Cerrar la tapa.\n\nSeleccionar:\n\n**Pressure Cook**\n\nAlta presión.\n\n**10 minutos.**",
             'technical_fundament' => 'Este tiempo permite que el bistec quede muy suave sin deshacerse.',
             'what_to_observe' => 'No abrir la tapa durante la cocción.',
             'common_errors' => 'Programar tiempos excesivos, ya que algunos cortes pueden desmoronarse.'],
            ['step_number' => 5, 'action' => "Realizar una **Liberación Rápida**.\n\nAbrir la tapa.\n\nSi la salsa está muy líquida, seleccionar **Sauté** durante 3 a 5 minutos.",
             'technical_fundament' => 'La reducción final concentra los sabores y mejora la consistencia de la salsa.',
             'what_to_observe' => 'La salsa debe cubrir ligeramente la cuchara.',
             'common_errors' => 'Reducir demasiado la salsa hasta secarla.'],
            ['step_number' => 6, 'action' => "Agregar cilantro fresco.\n\nMezclar suavemente.\n\nServir inmediatamente.",
             'technical_fundament' => 'El cilantro aporta frescura y aroma al finalizar la cocción.',
             'what_to_observe' => 'El cilantro debe conservar su color verde brillante.',
             'common_errors' => 'Agregar el cilantro antes de Pressure Cook.'],
        ]);

        $this->addVariants($recipe, [
            ['name' => 'Con papas', 'description' => 'Agregar 3 papas en cubos grandes antes de iniciar Pressure Cook.'],
        ]);

        $this->addAdaptations($recipe, [
            ['scenario' => 'Carne congelada', 'adaptation_text' => "No se recomienda sellar.\n\nAumentar **Pressure Cook** a **15 minutos**.\n\nMantener liberación rápida."],
        ]);

        $this->addConcepts($recipe, [
            'Sellado de carne.', 'Deglasado del fondo de la olla.', 'Reacción de Maillard.',
            'Formación de salsa con jitomate fresco.', 'Reducción mediante Sauté.', 'Ajuste final de consistencia.',
        ]);

        $this->addErrors($recipe, [
            ['problem' => 'La carne quedó dura', 'possible_cause' => 'El corte requería mayor tiempo de cocción.', 'solution' => 'Agregar 5 minutos adicionales de Pressure Cook.'],
        ]);
    }

    // ================================================================
    // 5. CALDO DE RES TRADICIONAL (id=19)
    // ================================================================
    private function seedCaldoDeRes(): void
    {
        $recipe = $this->createRecipe([
            'name' => 'Caldo de Res Tradicional',
            'slug' => 'caldo-de-res',
            'description' => 'Preparar un caldo de res claro, aromático y lleno de sabor, con carne suave que se desprenda fácilmente del hueso y verduras cocidas en su punto, evitando que se deshagan durante la cocción.',
            'objective' => 'Preparar un caldo de res claro, aromático y lleno de sabor, con carne suave que se desprenda fácilmente del hueso y verduras cocidas en su punto, evitando que se deshagan durante la cocción.',
            'prep_time' => 20,
            'cook_time' => 35,
            'total_time' => 75,
            'servings' => 6,
            'difficulty' => 2,
            'cost' => '$$$',
            'pressure_cook_time' => 4,
            'pressure_release' => 'natural',
            'chef_notes' => "- El **chambarete con hueso** produce un caldo con mucho más cuerpo que la pulpa de res, debido al colágeno y la médula presentes en el hueso.\n- Si deseas un caldo aún más intenso, puedes sellar la carne durante **5 minutos en Sauté** antes de agregar el agua. El resultado será un caldo más oscuro gracias a la **reacción de Maillard**.\n- Para un resultado óptimo, procura que las verduras tengan tamaños similares; así todas alcanzarán su punto de cocción al mismo tiempo.",
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('sopas') => ['is_primary' => true],
            $this->cat('res') => ['is_primary' => false],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('sopa'), $this->tag('res'), $this->tag('verduras'),
            $this->tag('comfort-food'), $this->tag('instant-pot'), $this->tag('Una olla'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot Duo Plus'),
            $this->eq('Cuchillo'),
            $this->eq('Tabla para cortar'),
            $this->eq('Espumadera'),
            $this->eq('Pinzas'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Chambarete de res con hueso', 'category' => 'proteinas', 'quantity' => 1.5, 'unit' => 'kg', 'notes' => 'Trozos grandes de 6 a 8 cm'],
            ['name' => 'Agua', 'category' => 'liquidos', 'quantity' => 2.5, 'unit' => 'litros'],
            ['name' => 'Cebolla blanca', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza', 'notes' => 'Mitades'],
            ['name' => 'Ajo', 'category' => 'verduras', 'quantity' => 4, 'unit' => 'dientes', 'notes' => 'Enteros'],
            ['name' => 'Laurel', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'hojas'],
            ['name' => 'Pimienta negra', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Sal', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharada'],
            ['name' => 'Zanahorias', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'piezas', 'notes' => 'Trozos de 4 cm'],
            ['name' => 'Papas', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'piezas', 'notes' => 'Cubos grandes de 5 cm'],
            ['name' => 'Elotes', 'category' => 'verduras', 'quantity' => 2, 'unit' => 'piezas', 'notes' => 'Cortados en tercios'],
            ['name' => 'Calabacitas', 'category' => 'verduras', 'quantity' => 2, 'unit' => 'piezas', 'notes' => 'Trozos de 4 cm'],
            ['name' => 'Chayote', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza', 'notes' => 'Cubos grandes'],
            ['name' => 'Cilantro fresco', 'category' => 'terminacion', 'notes' => 'Picado'],
            ['name' => 'Limones', 'category' => 'terminacion', 'notes' => 'Mitades'],
            ['name' => 'Cebolla picada', 'category' => 'terminacion'],
            ['name' => 'Chile serrano', 'category' => 'terminacion', 'is_optional' => true, 'notes' => 'Picado (opcional)'],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => "Colocar en la Instant Pot:\n\n- Carne.\n- Cebolla.\n- Ajo.\n- Laurel.\n- Pimienta.\n- Agua.\n\nAgregar la sal.",
             'technical_fundament' => 'La primera cocción desarrolla el caldo y permite que el colágeno del hueso se disuelva lentamente.',
             'what_to_observe' => 'El agua debe cubrir completamente la carne sin sobrepasar la línea máxima de la olla.',
             'common_errors' => 'Agregar las verduras desde el inicio.'],
            ['step_number' => 2, 'action' => "Seleccionar:\n\n**Pressure Cook**\n\nAlta presión\n\n35 minutos.",
             'technical_fundament' => 'Este tiempo permite ablandar el chambarete y extraer sabor del hueso sin que el caldo pierda claridad.',
             'what_to_observe' => 'No abrir la tapa durante la cocción.',
             'common_errors' => 'Reducir el tiempo pensando que la carne terminará de cocinarse después.'],
            ['step_number' => 3, 'action' => "Realizar una **Liberación Natural de 15 minutos**.\n\nDespués liberar manualmente el resto de la presión.",
             'technical_fundament' => 'La liberación natural mantiene la carne más jugosa y evita una ebullición brusca del caldo.',
             'what_to_observe' => 'La válvula debe bajar completamente antes de abrir la tapa.',
             'common_errors' => 'Abrir inmediatamente con liberación rápida.'],
            ['step_number' => 4, 'action' => "Agregar:\n\n- Papas.\n- Zanahorias.\n- Elote.\n- Chayote.\n\nCerrar nuevamente la olla.\n\nSeleccionar:\n\n**Pressure Cook**\n\nAlta presión\n\n**4 minutos**.",
             'technical_fundament' => 'Estas verduras requieren más tiempo de cocción que las calabacitas.',
             'what_to_observe' => 'Las verduras deben quedar apenas cubiertas por el caldo.',
             'common_errors' => 'Agregar también las calabacitas en este paso.'],
            ['step_number' => 5, 'action' => "Realizar una **Liberación Rápida**.\n\nAgregar las calabacitas.\n\nCerrar únicamente la tapa (sin presión).\n\nSeleccionar:\n\n**Keep Warm**\n\nDurante **8 minutos**.",
             'technical_fundament' => 'El calor residual cocina perfectamente las calabacitas sin deshacerse.',
             'what_to_observe' => 'Las calabacitas deben conservar su color verde.',
             'common_errors' => 'Cocinarlas bajo presión.'],
            ['step_number' => 6, 'action' => "Rectificar la sal.\n\nRetirar la cebolla y las hojas de laurel.\n\nServir inmediatamente.",
             'technical_fundament' => 'La cebolla y el laurel ya aportaron todo su sabor y no es necesario servirlos.',
             'what_to_observe' => 'El caldo debe verse claro y ligeramente dorado.',
             'common_errors' => 'Agregar más sal antes de probar el caldo.'],
        ]);

        $this->addVariants($recipe, [
            ['name' => 'Con arroz', 'description' => 'Agregar arroz cocido al momento de servir.'],
        ]);

        $this->addAdaptations($recipe, [
            ['scenario' => 'Carne congelada', 'adaptation_text' => "Aumentar la primera cocción a:\n\n**45 minutos**\n\nMantener la liberación natural de 15 minutos."],
        ]);

        $this->addConcepts($recipe, [
            'Elaboración de un fondo de res.', 'Extracción de colágeno.', 'Cocción por etapas.',
            'Liberación Natural.', 'Uso del calor residual.', 'Cocción diferenciada según el tipo de verdura.',
            'Ajuste final de sazón.',
        ]);

        $this->addErrors($recipe, [
            ['problem' => 'La carne quedó dura', 'possible_cause' => 'Tiempo insuficiente de Pressure Cook.', 'solution' => 'Agregar otros 10 minutos de Pressure Cook y realizar nuevamente una liberación natural.'],
        ]);
    }

    // ================================================================
    // 6. PICADILLO CON PAPAS (id=20)
    // ================================================================
    private function seedPicadilloConPapas(): void
    {
        $recipe = $this->createRecipe([
            'name' => '🥔 Picadillo con Papas',
            'slug' => 'picadillo-con-papas',
            'description' => 'Preparar un picadillo tradicional mexicano con carne molida, papas y zanahorias cocidas en su punto, acompañado de una salsa ligera de jitomate que conserve todo el sabor de los ingredientes sin convertir las verduras en puré.',
            'objective' => 'Preparar un picadillo tradicional mexicano con carne molida, papas y zanahorias cocidas en su punto, acompañado de una salsa ligera de jitomate que conserve todo el sabor de los ingredientes sin convertir las verduras en puré.',
            'prep_time' => 15,
            'cook_time' => 5,
            'total_time' => 35,
            'servings' => 6,
            'difficulty' => 1,
            'cost' => '$$',
            'pressure_cook_time' => 5,
            'pressure_release' => 'rapida',
            'saute_time' => 10,
            'chef_notes' => "- El picadillo tradicional mexicano admite muchas variaciones. Puedes incorporar **chícharos, elote, aceitunas, pasas o calabacita** sin modificar el tiempo principal de cocción, siempre que los ingredientes más delicados se agreguen después de liberar la presión.\n- Si prefieres un picadillo más caldoso para acompañar arroz, omite la reducción final en **Sauté**.\n- Este platillo combina muy bien con **arroz rojo**, **frijoles de la olla**, **frijoles puercos**, tortillas de maíz o tostadas.",
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('res') => ['is_primary' => true],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('carne-molida'), $this->tag('picadillo'), $this->tag('papas'),
            $this->tag('comida-casera'), $this->tag('instant-pot'), $this->tag('Una olla'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot Duo Plus'),
            $this->eq('Espátula de madera'),
            $this->eq('Tabla para cortar'),
            $this->eq('Cuchillo'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Carne molida de res (90/10 o 85/15)', 'category' => 'proteinas', 'quantity' => 1, 'unit' => 'kg'],
            ['name' => 'Jitomate Roma', 'category' => 'verduras', 'quantity' => 4, 'unit' => 'piezas', 'notes' => 'Picado en cubos'],
            ['name' => 'Papas', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'piezas', 'notes' => 'Cubos de 2 cm'],
            ['name' => 'Zanahorias', 'category' => 'verduras', 'quantity' => 2, 'unit' => 'piezas', 'notes' => 'Cubos de 1.5 cm'],
            ['name' => 'Cebolla blanca', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza', 'notes' => 'Picada'],
            ['name' => 'Ajo', 'category' => 'verduras', 'quantity' => 3, 'unit' => 'dientes', 'notes' => 'Picado'],
            ['name' => 'Caldo de res', 'category' => 'liquidos', 'quantity' => 1, 'unit' => 'taza'],
            ['name' => 'Orégano seco', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Comino molido', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Pimienta negra', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Sal', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Cilantro fresco', 'category' => 'terminacion', 'is_optional' => true, 'notes' => 'Picado (opcional)'],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => "Seleccionar **Sauté** (Nivel Alto).\n\nAgregar una cucharada de aceite si la carne es muy magra.\n\nIncorporar la carne molida.\n\nDeshacerla con una espátula.\n\nCocinar durante 5 minutos.",
             'technical_fundament' => 'El dorado desarrolla sabor mediante la reacción de Maillard y evita que la carne quede con textura hervida.',
             'what_to_observe' => 'La carne debe quedar completamente separada y ligeramente dorada.',
             'common_errors' => 'Mover constantemente la carne sin dejar que tome color.'],
            ['step_number' => 2, 'action' => "Agregar:\n\n- Cebolla.\n- Ajo.\n\nCocinar durante 2 minutos.\n\nAgregar el jitomate.\n\nCocinar otros 3 minutos.",
             'technical_fundament' => 'El jitomate libera líquido que ayuda a despegar los residuos del fondo de la olla.',
             'what_to_observe' => 'El jitomate comenzará a deshacerse formando una salsa ligera.',
             'common_errors' => 'No desglasar completamente el fondo.'],
            ['step_number' => 3, 'action' => "Agregar:\n\n- Papas.\n- Zanahorias.\n- Caldo.\n- Orégano.\n- Comino.\n- Sal.\n- Pimienta.\n\nMezclar suavemente.",
             'technical_fundament' => 'El caldo aporta humedad suficiente para generar presión sin volver aguado el picadillo.',
             'what_to_observe' => 'Las papas deben quedar distribuidas de manera uniforme.',
             'common_errors' => 'Agregar demasiada agua.'],
            ['step_number' => 4, 'action' => "Cerrar la tapa.\n\nSeleccionar:\n\n**Pressure Cook**\n\nAlta presión.\n\n**5 minutos.**",
             'technical_fundament' => 'La carne ya está cocida; únicamente se busca terminar la cocción de las papas y concentrar los sabores.',
             'what_to_observe' => 'No abrir la tapa durante la cocción.',
             'common_errors' => 'Programar tiempos largos que provoquen que las papas se deshagan.'],
            ['step_number' => 5, 'action' => "Realizar una **Liberación Rápida**.\n\nAbrir la tapa.\n\nSi el picadillo tiene demasiado líquido, seleccionar **Sauté** durante 3 a 5 minutos.",
             'technical_fundament' => 'La reducción final permite obtener un picadillo jugoso, pero no caldoso.',
             'what_to_observe' => 'La salsa debe cubrir ligeramente la carne sin acumular líquido en el fondo.',
             'common_errors' => 'Reducir demasiado hasta secar completamente el platillo.'],
            ['step_number' => 6, 'action' => "Agregar cilantro fresco (opcional).\n\nServir inmediatamente.",
             'technical_fundament' => 'El cilantro aporta frescura sin alterar el sabor principal.',
             'what_to_observe' => 'Las papas deben conservar perfectamente su forma.',
             'common_errors' => 'Remover vigorosamente al final y romper las papas.'],
        ]);

        $this->addVariants($recipe, [
            ['name' => 'Con chícharos', 'description' => "Agregar una taza de chícharos congelados después de liberar la presión.\n\nMezclar durante 2 minutos utilizando **Keep Warm**."],
        ]);

        $this->addAdaptations($recipe, [
            ['scenario' => 'Carne congelada', 'adaptation_text' => 'No recomendable. Descongelar previamente para poder dorarla correctamente.'],
        ]);

        $this->addConcepts($recipe, [
            'Sellado de carne molida.', 'Deglasado.', 'Formación de salsa con jitomate.',
            'Cocción corta bajo presión.', 'Reducción mediante Sauté.', 'Control del punto de cocción de las papas.',
        ]);

        $this->addErrors($recipe, [
            ['problem' => 'Las papas se deshicieron', 'possible_cause' => 'Se cocinaron demasiado tiempo o se cortaron muy pequeñas.', 'solution' => 'Utilizar cubos de aproximadamente 2 cm y respetar los 5 minutos de Pressure Cook.'],
        ]);
    }

    // ================================================================
    // 7. FRIJOLES DE LA OLLA (id=21)
    // ================================================================
    private function seedFrijolesDeLaOlla(): void
    {
        $recipe = $this->createRecipe([
            'name' => '🫘 Frijoles de la Olla',
            'slug' => 'frijoles-de-la-olla',
            'description' => 'Preparar frijoles suaves, cremosos y con un caldo lleno de sabor, ideales para servirse solos o utilizarse posteriormente en frijoles refritos, frijoles puercos, charros y muchas otras recetas.',
            'objective' => 'Preparar frijoles suaves, cremosos y con un caldo lleno de sabor, ideales para servirse solos o utilizarse posteriormente en frijoles refritos, frijoles puercos, charros y muchas otras recetas.',
            'prep_time' => 10,
            'cook_time' => 45,
            'total_time' => 85,
            'servings' => 10,
            'difficulty' => 1,
            'cost' => '$',
            'storage_refrigeration' => '5 días.',
            'storage_freezing' => '6 meses.',
            'pressure_cook_time' => 45,
            'pressure_release' => 'natural',
            'chef_notes' => 'Estos frijoles sirven como base para preparar frijoles puercos, charros, refritos y enfrijoladas.',
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('legumbres') => ['is_primary' => true],
            $this->cat('frijoles') => ['is_primary' => false],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('frijoles'), $this->tag('básicos'), $this->tag('Meal Prep'),
            $this->tag('instant-pot'), $this->tag('mexicanos'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot Duo Plus'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Frijol pinto o peruano', 'category' => 'condimentos', 'quantity' => 500, 'unit' => 'g', 'notes' => 'Lavado'],
            ['name' => 'Agua', 'category' => 'liquidos', 'quantity' => 2.5, 'unit' => 'litros'],
            ['name' => 'Cebolla blanca', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza', 'notes' => 'Mitades'],
            ['name' => 'Ajo', 'category' => 'verduras', 'quantity' => 4, 'unit' => 'dientes', 'notes' => 'Enteros'],
            ['name' => 'Laurel', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'hojas'],
            ['name' => 'Sal', 'category' => 'condimentos', 'notes' => 'Al gusto'],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => "Agregar a la olla:\n\n- Frijoles.\n- Agua.\n- Cebolla.\n- Ajo.\n- Laurel.",
             'technical_fundament' => 'Los aromáticos infusionan el caldo durante toda la cocción.',
             'what_to_observe' => 'No llenar la olla por encima de la línea de seguridad.',
             'common_errors' => 'Agregar sal desde el inicio en grandes cantidades.'],
            ['step_number' => 2, 'action' => "Seleccionar:\n\n**Pressure Cook**\n\nAlta presión.\n\n45 minutos.",
             'technical_fundament' => 'Este tiempo produce frijoles muy suaves sin deshacerse.',
             'what_to_observe' => 'No abrir durante la cocción.',
             'common_errors' => 'Reducir el tiempo pensando que terminarán de cocerse después.'],
            ['step_number' => 3, 'action' => "Realizar una **Liberación Natural de 20 minutos**.\n\nDespués liberar el resto de la presión.",
             'technical_fundament' => 'Evita que los frijoles revienten.',
             'what_to_observe' => 'Los frijoles deben permanecer enteros.',
             'common_errors' => 'Liberación rápida inmediata.'],
            ['step_number' => 4, 'action' => "Agregar sal.\n\nMezclar.\n\nReposar 10 minutos.",
             'technical_fundament' => 'La sal termina de equilibrar el sabor.',
             'what_to_observe' => 'Probar antes de agregar más sal.',
             'common_errors' => 'Agregar demasiada sal.'],
        ]);

        $this->addVariants($recipe, [
            ['name' => 'Con tocino', 'description' => 'Agregar antes de Pressure Cook.'],
            ['name' => 'Con chorizo', 'description' => 'Agregar previamente dorado.'],
            ['name' => 'Con epazote', 'description' => 'Agregar durante los últimos 10 minutos de cocción.'],
        ]);

        $this->addAdaptations($recipe, [
            ['scenario' => 'Frijoles remojados', 'adaptation_text' => 'Pressure Cook 25 minutos.'],
            ['scenario' => 'Frijoles negros', 'adaptation_text' => '35 minutos.'],
            ['scenario' => 'Frijol flor de mayo', 'adaptation_text' => '40 minutos.'],
        ]);

        $this->addConcepts($recipe, [
            'Cocción de legumbres.', 'Liberación natural.', 'Hidratación bajo presión.', 'Conservación.',
        ]);

        $this->addErrors($recipe, [
            ['problem' => 'Frijoles duros', 'possible_cause' => 'Grano viejo.', 'solution' => 'Agregar 10 a 15 minutos más.'],
        ]);
    }

    // ================================================================
    // 8. CALDO DE PAPAS (id=22)
    // ================================================================
    private function seedCaldoDePapas(): void
    {
        $recipe = $this->createRecipe([
            'name' => '🥔 Caldo de Papas',
            'slug' => 'caldo-de-papas',
            'description' => 'Preparar un caldo ligero con papas tiernas y un caldo aromático ideal como entrada o como comida ligera.',
            'objective' => 'Preparar un caldo ligero con papas tiernas y un caldo aromático ideal como entrada o como comida ligera.',
            'prep_time' => 15,
            'cook_time' => 4,
            'total_time' => 30,
            'servings' => 6,
            'difficulty' => 1,
            'cost' => '$',
            'storage_refrigeration' => '4 días.',
            'storage_freezing' => 'No recomendado debido a la textura de la papa.',
            'pressure_cook_time' => 4,
            'pressure_release' => 'rapida',
            'saute_time' => 3,
            'chef_notes' => 'Para convertir este caldo en una comida más completa puedes agregar pollo deshebrado, queso panela, tocino dorado o chile poblano asado. También puedes licuar una o dos papas cocidas con un poco de caldo y reincorporarlas para obtener una versión más cremosa sin necesidad de utilizar crema.',
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('sopas') => ['is_primary' => true],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('sopa'), $this->tag('papa'), $this->tag('económica'),
            $this->tag('instant-pot'), $this->tag('vegetariana'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot Duo Plus'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Papa blanca', 'category' => 'verduras', 'quantity' => 6, 'unit' => 'piezas', 'notes' => 'Cubos grandes'],
            ['name' => 'Cebolla', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza', 'notes' => 'Picada'],
            ['name' => 'Ajo', 'category' => 'verduras', 'quantity' => 2, 'unit' => 'dientes', 'notes' => 'Picado'],
            ['name' => 'Zanahoria', 'category' => 'verduras', 'quantity' => 2, 'unit' => 'piezas', 'notes' => 'Rodajas'],
            ['name' => 'Caldo de pollo o verduras', 'category' => 'liquidos', 'quantity' => 1.5, 'unit' => 'litros'],
            ['name' => 'Orégano', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Sal', 'category' => 'condimentos'],
            ['name' => 'Pimienta', 'category' => 'condimentos'],
            ['name' => 'Cilantro fresco', 'category' => 'terminacion', 'notes' => 'Picado'],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => "Seleccionar **Sauté**.\n\nSofreír cebolla y ajo durante 3 minutos.",
             'technical_fundament' => 'Desarrolla un caldo con mayor sabor.',
             'what_to_observe' => 'La cebolla debe verse transparente.',
             'common_errors' => 'Quemar el ajo.'],
            ['step_number' => 2, 'action' => "Agregar:\n\n- Zanahorias.\n- Papas.\n- Caldo.\n- Orégano.\n- Sal.\n- Pimienta.",
             'technical_fundament' => 'Las verduras comenzarán a liberar sabor al caldo.',
             'what_to_observe' => 'El líquido debe cubrir apenas las verduras.',
             'common_errors' => 'Agregar demasiada agua.'],
            ['step_number' => 3, 'action' => "Seleccionar:\n\n**Pressure Cook**\n\nAlta presión.\n\n4 minutos.",
             'technical_fundament' => 'Las papas terminan perfectamente cocidas sin romperse.',
             'what_to_observe' => 'No exceder el tiempo.',
             'common_errors' => 'Programar más de 8 minutos.'],
            ['step_number' => 4, 'action' => "Liberación rápida.\n\nAgregar cilantro fresco.\n\nServir.",
             'technical_fundament' => 'El cilantro conserva mejor su aroma cuando se agrega al final.',
             'what_to_observe' => 'Las papas deben conservar su forma.',
             'common_errors' => 'Remover vigorosamente.'],
        ]);

        $this->addVariants($recipe, [
            ['name' => 'Con queso panela', 'description' => 'Agregar al servir.'],
            ['name' => 'Con pollo deshebrado', 'description' => 'Agregar después de liberar presión.'],
            ['name' => 'Con chile poblano', 'description' => 'Agregar durante el sofrito.'],
            ['name' => 'Con elote', 'description' => 'Agregar junto con las papas.'],
        ]);

        $this->addAdaptations($recipe, [
            ['scenario' => 'Instant Pot 8 Qt', 'adaptation_text' => 'Agregar 250 ml más de caldo.'],
            ['scenario' => 'Media receta', 'adaptation_text' => 'Mismos tiempos.'],
        ]);

        $this->addConcepts($recipe, [
            'Sofrito.', 'Cocción corta de verduras.', 'Caldos ligeros.', 'Uso del calor residual.',
        ]);

        $this->addErrors($recipe, [
            ['problem' => 'Las papas se deshicieron', 'possible_cause' => 'Tiempo excesivo.', 'solution' => 'Respetar los 4 minutos de Pressure Cook.'],
        ]);
    }

    // ================================================================
    // 9. TOSTADAS Y SOPES DE CARNE DESHEBRADA (id=23)
    // ================================================================
    private function seedTostadasSopes(): void
    {
        $recipe = $this->createRecipe([
            'name' => 'Tostadas y Sopes de Carne Deshebrada',
            'slug' => 'tostadas-sopes-carne-deshebrada',
            'description' => 'Preparar una carne de res muy suave y jugosa, ideal para utilizar como relleno de tostadas, sopes, tacos, burritos o quesadillas, obteniendo una carne que absorba completamente el sabor de su propio caldo.',
            'objective' => 'Preparar una carne de res muy suave y jugosa, ideal para utilizar como relleno de tostadas, sopes, tacos, burritos o quesadillas, obteniendo una carne que absorba completamente el sabor de su propio caldo.',
            'prep_time' => 20,
            'cook_time' => 50,
            'total_time' => 90,
            'servings' => 6,
            'difficulty' => 2,
            'cost' => '$$$',
            'storage_refrigeration' => '5 días.',
            'pressure_cook_time' => 50,
            'pressure_release' => 'natural',
            'saute_time' => 10,
            'chef_notes' => "- Los mejores cortes para deshebrar son **chambarete, espaldilla, diezmillo, aguja y chuck roast**, debido a su contenido de colágeno.\n- Si preparas una cantidad grande, guarda la carne **mezclada con un poco de su caldo**. Esto evita que se reseque durante la refrigeración y al recalentarla.\n- Esta carne puede utilizarse también para **tacos dorados, enchiladas, burritos, quesadillas, tortas, sincronizadas y chilaquiles con carne**.",
            'is_published' => true,
            'recipe_type' => 'base',
        ]);

        $recipe->categories()->syncWithoutDetaching([
            $this->cat('antojitos') => ['is_primary' => true],
        ]);

        $recipe->tags()->syncWithoutDetaching([
            $this->tag('res'), $this->tag('tostadas'), $this->tag('sopes'),
            $this->tag('antojitos'), $this->tag('carne-deshebrada'), $this->tag('instant-pot'),
        ]);

        $recipe->equipment()->syncWithoutDetaching([
            $this->eq('Instant Pot Duo Plus'),
            $this->eq('Dos tenedores'),
            $this->eq('Pinzas'),
        ]);

        $this->addIngredients($recipe, [
            ['name' => 'Chambarete, diezmillo o espaldilla de res', 'category' => 'proteinas', 'quantity' => 1.5, 'unit' => 'kg', 'notes' => 'Trozos grandes'],
            ['name' => 'Cebolla blanca', 'category' => 'verduras', 'quantity' => 1, 'unit' => 'pieza', 'notes' => 'Mitades'],
            ['name' => 'Ajo', 'category' => 'verduras', 'quantity' => 5, 'unit' => 'dientes', 'notes' => 'Enteros'],
            ['name' => 'Caldo de res', 'category' => 'liquidos', 'quantity' => 1, 'unit' => 'taza'],
            ['name' => 'Laurel', 'category' => 'condimentos', 'quantity' => 2, 'unit' => 'hojas'],
            ['name' => 'Comino', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Orégano', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Pimienta negra', 'category' => 'condimentos', 'quantity' => 1, 'unit' => 'cucharadita'],
            ['name' => 'Sal', 'category' => 'condimentos', 'notes' => 'Al gusto'],
            ['name' => 'Cilantro', 'category' => 'terminacion', 'notes' => 'Picado'],
        ]);

        $this->addSteps($recipe, [
            ['step_number' => 1, 'action' => "Seleccionar **Sauté**.\n\nAgregar una cucharada de aceite.\n\nSellar la carne durante 6 a 8 minutos.",
             'technical_fundament' => 'El sellado desarrolla sabores mediante la reacción de Maillard.',
             'what_to_observe' => 'La carne debe presentar una costra ligeramente dorada.',
             'common_errors' => 'Mover constantemente la carne.'],
            ['step_number' => 2, 'action' => "Agregar:\n\n- Cebolla.\n- Ajo.\n\nCocinar durante 2 minutos.\n\nIncorporar:\n\n- Laurel.\n- Comino.\n- Orégano.\n- Pimienta.\n- Caldo.\n\nDesglasar completamente el fondo.",
             'technical_fundament' => 'El caldo recupera todos los sabores adheridos al fondo de la olla.',
             'what_to_observe' => 'No deben quedar residuos pegados.',
             'common_errors' => 'Cerrar la olla sin desglasar.'],
            ['step_number' => 3, 'action' => "Seleccionar:\n\n**Pressure Cook**\n\nAlta presión\n\n50 minutos.",
             'technical_fundament' => 'Este tiempo rompe el tejido conectivo y produce una carne extremadamente suave.',
             'what_to_observe' => 'No abrir durante la cocción.',
             'common_errors' => 'Reducir el tiempo.'],
            ['step_number' => 4, 'action' => "Realizar una **Liberación Natural de 15 minutos**.\n\nDespués liberar el resto de la presión.",
             'technical_fundament' => 'Permite que los jugos permanezcan dentro de la carne.',
             'what_to_observe' => 'La válvula debe bajar completamente.',
             'common_errors' => 'Liberación rápida inmediata.'],
            ['step_number' => 5, 'action' => "Retirar la carne.\n\nDeshebrarla utilizando dos tenedores.",
             'technical_fundament' => 'La carne debe separarse prácticamente sola.',
             'what_to_observe' => 'No deben quedar fibras largas.',
             'common_errors' => 'Cortar con cuchillo.'],
            ['step_number' => 6, 'action' => "Seleccionar **Sauté**.\n\nReducir el caldo durante 8 minutos.\n\nRegresar la carne.\n\nMezclar.",
             'technical_fundament' => 'La carne vuelve a absorber parte del caldo concentrado.',
             'what_to_observe' => 'Debe quedar jugosa, nunca seca.',
             'common_errors' => 'Reducir completamente el líquido.'],
        ]);

        $this->addVariants($recipe, [
            ['name' => 'Con chile guajillo', 'description' => 'Licuar 3 chiles hidratados con el caldo.'],
        ]);

        $this->addAdaptations($recipe, [
            ['scenario' => 'Carne congelada', 'adaptation_text' => "Pressure Cook\n\n60 minutos.\n\nLiberación Natural\n\n20 minutos."],
        ]);

        $this->addConcepts($recipe, [
            'Sellado de carne.', 'Reacción de Maillard.', 'Desglasado.',
            'Extracción de colágeno.', 'Deshebrado.', 'Reducción de caldo.', 'Rehidratación de la carne.',
        ]);

        $this->addErrors($recipe, [
            ['problem' => 'La carne quedó dura', 'possible_cause' => 'Tiempo insuficiente.', 'solution' => 'Agregar 10 minutos más de Pressure Cook.'],
        ]);
    }
}
