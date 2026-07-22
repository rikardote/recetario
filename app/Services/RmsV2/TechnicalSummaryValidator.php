<?php

namespace App\Services\RmsV2;

/**
 * Validates the technical summary section.
 *
 * Must be a Markdown table with exactly 2 columns: | Función | Tiempo |
 */
class TechnicalSummaryValidator
{
    /**
     * Parse technical summary table.
     * Returns ['summary' => [...], 'errors' => []].
     */
    public function validate(string $content): array
    {
        $errors = [];
        $summary = [];

        $lines = explode("\n", trim($content));
        if (empty($lines)) {
            $errors[] = 'rms-v2-resumen-vacio: La sección Resumen técnico está vacía.';
            return ['summary' => $summary, 'errors' => $errors];
        }

        // Find table rows (lines starting and ending with |)
        $inTable = false;
        $headerRow = null;

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip separator lines like |---|---|
            if (preg_match('/^\|[\s\-:]+\|[\s\-:]+\|$/', $line)) {
                $inTable = true;
                continue;
            }

            // Table row
            if (preg_match('/^\|(.+)\|(.+)\|$/', $line, $m)) {
                $col1 = trim($m[1]);
                $col2 = trim($m[2]);

                if (!$inTable) {
                    // This is the header row
                    $inTable = true;
                    continue;
                }

                $summary[] = [
                    'funcion' => $col1,
                    'tiempo' => $col2,
                ];
            }
        }

        if (empty($summary)) {
            $errors[] = 'rms-v2-resumen-vacio: No se encontraron filas en la tabla del Resumen técnico.';
        }

        // Map to recipe fields
        $mapped = $this->mapToRecipeFields($summary);

        return ['summary' => $mapped, 'errors' => $errors];
    }

    private function mapToRecipeFields(array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $funcion = mb_strtolower($row['funcion']);
            $tiempo = $row['tiempo'];

            // Extract minutes
            $minutes = 0;
            if (preg_match('/(\d+)/', $tiempo, $m)) {
                $minutes = (int) $m[1];
            }

            if (str_contains($funcion, 'pressure cook')) {
                $result['pressure_cook_time'] = $minutes;
            } elseif (str_contains($funcion, 'sauté') || str_contains($funcion, 'saute')) {
                $result['saute_time'] = $minutes;
            } elseif (str_contains($funcion, 'liberación') || str_contains($funcion, 'liberacion')) {
                $result['pressure_release'] = str_contains($funcion, 'natural') || str_contains($tiempo, 'atural') ? 'natural' : 'rapida';
                if ($minutes > 0) {
                    $result['pressure_release_time'] = $minutes;
                }
            }
        }

        return $result;
    }
}
