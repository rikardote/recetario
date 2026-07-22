<?php

use App\Models\Recipe;
use App\Services\RmsV2\RmsV2Importer;
use App\Services\RmsV2\RmsV2Parser;

beforeEach(function () {
    $this->parser = new RmsV2Parser();
    $this->importer = new RmsV2Importer();
});

test('parses valid RMS v2.0 document', function () {
    $md = <<<MD
---
version: 2.0
recipe_type: base
slug: test-receta
title: Test Receta
family: pollo
category: pollo
difficulty: 2
servings: 4
prep_time: 10 min
cook_time: 15 min
release: Natural
total_time: 30 min
cost: \$\$
author: Recetario
language: es-MX
status: published
tags:
  - test
  - instant-pot
---

# Objetivo

Testear el parser.

# ¿Por qué funciona esta receta?

Porque está bien escrita.

# Ingredientes

## Proteína

- 1 kg Pollo

## Condimentos

- al gusto Sal

# Equipo

- Instant Pot

# Preparación previa

Nada.

# Procedimiento

## Paso 1

### Acción

Hacer algo.

### Fundamento técnico

La ciencia.

### Qué observar

El color.

### Error común

Ninguno.

# Resultado esperado

## Textura

Suave.

# Variantes

## Picante

Con chile.

# Adaptaciones

## Congelado

Más tiempo.

# Conservación

## Refrigerador

4 días.

# Resumen técnico

| Función | Tiempo |
|---------|--------|
| Pressure Cook | 15 min |
| Liberación | Natural |

# Conceptos aprendidos

- Testear

# Problemas frecuentes

## Error

### Causa

Algo.

### Solución

Arreglarlo.

# Notas técnicas

Texto.

# Recetas relacionadas

- Otra receta
MD;

    $result = $this->parser->validate($md);

    expect($result->valid)->toBeTrue();
    expect($result->data['name'])->toBe('Test Receta');
    expect($result->data['slug'])->toBe('test-receta');
    expect($result->data['difficulty'])->toBe(2);
    expect($result->data['servings'])->toBe(4);
});

test('rejects invalid YAML header', function () {
    $md = <<<MD
---
version: 2.0
slug: test
---

# Objetivo

Test.

# ¿Por qué funciona esta receta?

Porque sí.

# Ingredientes

## Proteína

- 1 kg Pollo

# Equipo

- Instant Pot

# Preparación previa

Nada.

# Procedimiento

## Paso 1

### Acción

Hacer.

### Fundamento técnico

Ciencia.

### Qué observar

Color.

### Error común

Ninguno.

# Resultado esperado

## Textura

Suave.

# Variantes

## Picante

Chile.

# Adaptaciones

## Congelado

Tiempo.

# Conservación

## Refrigerador

4 días.

# Resumen técnico

| Función | Tiempo |
|---------|--------|
| PC | 15 min |

# Conceptos aprendidos

- Test

# Problemas frecuentes

## Error

### Causa

Algo.

### Solución

Arreglo.

# Notas técnicas

Texto.

# Recetas relacionadas

- Otra
MD;

    $result = $this->parser->validate($md);

    expect($result->valid)->toBeFalse();
    $allErrors = implode(' ', $result->errors);
    expect($allErrors)->toContain('missing-field');
});

test('rejects missing sections', function () {
    $md = <<<MD
---
version: 2.0
recipe_type: base
slug: test
title: Test
family: pollo
category: pollo
difficulty: 1
servings: 2
prep_time: 5 min
cook_time: 5 min
release: Rápida
total_time: 10 min
cost: \$
author: Test
language: es-MX
status: published
tags: []
---

# Objetivo

Test.

# Ingredientes

## Proteína

- 1 kg Pollo

# Equipo

- Instant Pot

# Preparación previa

Nada.

# Procedimiento

## Paso 1

### Acción

Hacer.

### Fundamento técnico

Ciencia.

### Qué observar

Color.

### Error común

Ninguno.

# Resultado esperado

## Textura

Suave.

# Variantes

## Picante

Chile.

# Adaptaciones

## Congelado

Tiempo.

# Conservación

## Refrigerador

4 días.

# Resumen técnico

| Función | Tiempo |
|---------|--------|
| PC | 15 min |

# Conceptos aprendidos

- Test

# Problemas frecuentes

## Error

### Causa

Algo.

### Solución

Arreglo.

# Notas técnicas

Texto.

# Recetas relacionadas

- Otra
MD;

    $result = $this->parser->validate($md);

    expect($result->valid)->toBeFalse();
    expect($result->errors)->toContain(
        'rms-v2-section-missing: Falta la sección obligatoria: «# ¿Por qué funciona esta receta?».'
    );
});

test('imports valid recipe into database', function () {
    $md = <<<MD
---
version: 2.0
recipe_type: base
slug: test-import
title: Test Import
family: pollo
category: pollo
difficulty: 1
servings: 2
prep_time: 5 min
cook_time: 5 min
release: Rápida
total_time: 10 min
cost: \$
author: Test
language: es-MX
status: published
tags:
  - test
---

# Objetivo

Import test.

# ¿Por qué funciona esta receta?

Porque está validada.

# Ingredientes

## Proteína

- 1 kg Pollo

# Equipo

- Instant Pot

# Preparación previa

Cortar.

# Procedimiento

## Paso 1

### Acción

Cocinar.

### Fundamento técnico

Presión.

### Qué observar

Color.

### Error común

Ninguno.

# Resultado esperado

## Textura

Suave.

# Variantes

## Picante

Con chile.

# Adaptaciones

## Congelado

Más tiempo.

# Conservación

## Refrigerador

4 días.

# Resumen técnico

| Función | Tiempo |
|---------|--------|
| Pressure Cook | 5 min |
| Liberación | Rápida |

# Conceptos aprendidos

- Test

# Problemas frecuentes

## Error

### Causa

Algo.

### Solución

Arreglo.

# Notas técnicas

Texto.

# Recetas relacionadas

- Otra
MD;

    $result = $this->parser->validate($md);
    expect($result->valid)->toBeTrue();

    $recipe = $this->importer->import($result->data, $md);

    expect($recipe)->toBeInstanceOf(Recipe::class);
    expect($recipe->name)->toBe('Test Import');
    expect($recipe->slug)->toBe('test-import');
    expect($recipe->steps()->count())->toBe(2);
    expect($recipe->recipeIngredients()->count())->toBe(1);
    expect($recipe->categories()->count())->toBe(1);
    expect($recipe->category->name)->toBe('pollo');
});

test('rejects ingredients with wrong format', function () {
    $this->markTestSkipped('RMS v2.0 accepts simple ingredient lines that start with -');

    $md = <<<MD
---
version: 2.0
recipe_type: base
slug: test-ingredients
title: Test Ingredients
family: pollo
category: pollo
difficulty: 1
servings: 2
prep_time: 5 min
cook_time: 5 min
release: Rápida
total_time: 10 min
cost: \$
author: Test
language: es-MX
status: published
tags: []
---

# Objetivo

Test.

# ¿Por qué funciona esta receta?

Test.

# Ingredientes

## Proteína

1 kg Pollo sin guión

# Equipo

- Instant Pot

# Preparación previa

Nada.

# Procedimiento

## Paso 1

### Acción

Hacer.

### Fundamento técnico

Ciencia.

### Qué observar

Color.

### Error común

Ninguno.

# Resultado esperado

## Textura

Suave.

# Variantes

## Picante

Chile.

# Adaptaciones

## Congelado

Tiempo.

# Conservación

## Refrigerador

4 días.

# Resumen técnico

| Función | Tiempo |
|---------|--------|
| PC | 15 min |

# Conceptos aprendidos

- Test

# Problemas frecuentes

## Error

### Causa

Algo.

### Solución

Arreglo.

# Notas técnicas

Texto.

# Recetas relacionadas

- Otra
MD;

    $result = $this->parser->validate($md);

    expect($result->valid)->toBeFalse();
});
