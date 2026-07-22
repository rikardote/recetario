<?php

namespace App\Services\RmsV2;

/**
 * Extracts sections from an RMS v2.0 document by exact H1 headings.
 * All sections must appear in the specified order.
 */
class SectionExtractor
{
    /**
     * Ordered list of required section H1 headings.
     * Key = heading text (exact), Value = internal section key.
     */
    private const REQUIRED_SECTIONS = [
        'Objetivo' => 'objetivo',
        '¿Por qué funciona esta receta?' => 'por_que_funciona',
        'Ingredientes' => 'ingredientes',
        'Equipo' => 'equipo',
        'Preparación previa' => 'preparacion_previa',
        'Procedimiento' => 'procedimiento',
        'Resultado esperado' => 'resultado_esperado',
        'Variantes' => 'variantes',
        'Adaptaciones' => 'adaptaciones',
        'Conservación' => 'conservacion',
        'Resumen técnico' => 'resumen_tecnico',
        'Conceptos aprendidos' => 'conceptos_aprendidos',
        'Problemas frecuentes' => 'problemas_frecuentes',
        'Notas técnicas' => 'notas_tecnicas',
        'Recetas relacionadas' => 'recetas_relacionadas',
    ];

    /**
     * Extract sections from the markdown (after YAML frontmatter has been removed).
     *
     * Returns ['sections' => [...], 'errors' => [...]].
     */
    public function extract(string $body): array
    {
        $errors = [];
        $sections = [];

        // Remove YAML frontmatter if still present
        $body = preg_replace('/^---\s*\n.*?\n---\s*\n/s', '', $body);

        // Prepend newline for splitting
        $body = "\n" . $body;
        $parts = preg_split('/\n(?=#\s)/', $body);
        $firstKey = null;

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            // Extract the H1 heading
            if (!preg_match('/^#\s+(.+?)(?:\n|$)/u', $part, $m)) {
                continue;
            }

            $heading = trim($m[1]);

            // Check if this heading is one of the expected sections
            $found = false;
            foreach (self::REQUIRED_SECTIONS as $expectedHeading => $sectionKey) {
                if ($heading === $expectedHeading) {
                    $sections[$sectionKey] = trim(substr($part, strlen($m[0])));
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $errors[] = "rms-v2-section-unknown: Sección no reconocida: «{$heading}». Las secciones válidas son: " . implode(', ', array_keys(self::REQUIRED_SECTIONS));
            }
        }

        // Check that all required sections are present
        foreach (self::REQUIRED_SECTIONS as $heading => $sectionKey) {
            if (!isset($sections[$sectionKey])) {
                $errors[] = "rms-v2-section-missing: Falta la sección obligatoria: «# {$heading}».";
            }
        }

        return ['sections' => $sections, 'errors' => $errors];
    }
}
