# TAREA: Rediseñar el importador de recetas al estándar RMS v2.0

## Objetivo

Modificar completamente el importador de recetas para que utilice un formato **100% determinista**.

La aplicación **NO tendrá acceso a una IA**.

Las recetas serán generadas previamente por ChatGPT, Claude, Gemini u otro LLM.

Por lo tanto el contrato entre el LLM y Laravel será un documento Markdown llamado **RMS (Recipe Markdown Standard)**.

El importador NO debe intentar adivinar información.

Debe leer un documento siguiendo reglas exactas.

---

# Principios

El parser nunca utilizará heurísticas.

Nunca interpretará texto.

Nunca inferirá relaciones.

Nunca buscará palabras similares.

Todo deberá depender únicamente de la estructura del documento.

---

# Estructura del documento

Todas las recetas tendrán exactamente el siguiente orden.

1. YAML Header
2. Título
3. Objetivo
4. ¿Por qué funciona esta receta?
5. Ingredientes
6. Equipo
7. Preparación previa
8. Procedimiento
9. Resultado esperado
10. Variantes
11. Adaptaciones
12. Conservación
13. Resumen técnico
14. Conceptos aprendidos
15. Problemas frecuentes
16. Notas técnicas
17. Recetas relacionadas

El orden es obligatorio.

---

# YAML

El documento siempre comienza con:

```yaml
---
version: 2.0
recipe_type: base
slug: choripollo
title: Choripollo
family: pollo
category: pollo
difficulty: 2
servings: 4
prep_time: 15 min
cook_time: 6 min
release: Rapida
total_time: 35 min
cost: $$
author: Recetario
language: es-MX
status: published
tags:
  - pollo
  - instant-pot
---
```

Todos estos campos son obligatorios.

No existirá texto antes del YAML.

---

# Secciones

Cada sección principal utilizará EXACTAMENTE un H1.

Ejemplo.

# Objetivo

Nunca

## Objetivo

Nunca

### Objetivo

---

Los nombres permitidos son únicamente:

# Objetivo

# ¿Por qué funciona esta receta?

# Ingredientes

# Equipo

# Preparación previa

# Procedimiento

# Resultado esperado

# Variantes

# Adaptaciones

# Conservación

# Resumen técnico

# Conceptos aprendidos

# Problemas frecuentes

# Notas técnicas

# Recetas relacionadas

Si falta una sección el parser deberá indicarlo.

---

# Ingredientes

Los ingredientes estarán organizados por categorías.

Ejemplo.

## Proteína

- 1 kg pechuga de pollo

## Verduras

- 2 papas

- 1 cebolla

## Líquidos

- 1 taza caldo de pollo

## Condimentos

- sal

- pimienta

## Terminación

- queso

No intentar interpretar el texto.

Simplemente guardar:

Categoría

Descripción

Orden

---

# Equipo

Lista simple.

Ejemplo.

- Instant Pot

- Espátula

- Tabla

---

# Preparación previa

Texto libre.

---

# Procedimiento

Cada paso utilizará exactamente esta estructura.

## Paso 1

### Acción

Texto

### Fundamento técnico

Texto

### Qué observar

Texto

### Error común

Texto

Después

## Paso 2

etc.

Todos los pasos tendrán exactamente estos cuatro bloques.

No agregar otros.

No cambiar nombres.

---

# Resultado esperado

Utilizar subtítulos.

Ejemplo.

## Pollo

Texto

## Salsa

Texto

Guardar:

Título

Contenido

---

# Variantes

Utilizar subtítulos.

## Picante

Texto

## Con crema

Texto

---

# Adaptaciones

Mismo formato.

---

# Conservación

Texto libre.

---

# Resumen técnico

Siempre una tabla Markdown.

| Función | Tiempo |

Guardar todas las filas.

---

# Conceptos aprendidos

Lista.

- Sellado

- Deglasado

- Liberación rápida

---

# Problemas frecuentes

Cada problema tendrá.

## Problema

### Causa

Texto

### Solución

Texto

---

# Notas técnicas

Texto libre.

---

# Recetas relacionadas

Lista.

- Arroz Rojo

- Frijoles

- Mole

---

# Parser

El parser NO utilizará expresiones heurísticas.

Debe localizar las secciones únicamente por sus encabezados.

Nunca buscar palabras parecidas.

Nunca asumir títulos.

---

# Validaciones

Antes de importar validar.

✓ YAML válido

✓ Slug

✓ Version

✓ Recipe Type

✓ Todas las secciones existen

✓ Todos los pasos contienen:

Acción

Fundamento técnico

Qué observar

Error común

✓ Existe Resumen Técnico

✓ Existe Conservación

✓ Existe Objetivo

Si falta información mostrar exactamente qué sección falta.

No intentar corregir automáticamente.

---

# Objetivo del proyecto

El objetivo NO es importar cualquier Markdown.

El objetivo es importar únicamente documentos compatibles con RMS v2.0.

Si el documento no cumple la especificación deberá rechazarse indicando los errores encontrados.

Nunca intentar adivinar información.

Nunca modificar el contenido automáticamente.

El importador debe ser completamente determinista.
