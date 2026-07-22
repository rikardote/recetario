<?php

namespace App\Services\RmsV2;

/**
 * Validates all sections of an RMS v2.0 document.
 */
class SectionValidators
{
    public function validateText(string $content, string $sectionName): array
    {
        $text = trim($content);
        if (empty($text)) {
            return ['text' => '', 'errors' => ["rms-v2-{$sectionName}-empty: La sección {$sectionName} está vacía."]];
        }
        return ['text' => $text, 'errors' => []];
    }

    /**
     * Parse H2-based sections (Resultado, Variantes, Adaptaciones).
     * Format:
     * ## Subtítulo
     * Contenido libre
     */
    public function validateH2Section(string $content, string $sectionName): array
    {
        $errors = [];
        $items = [];

        $content = "\n" . $content;
        $parts = preg_split('/\n(?=##\s)/u', $content);
        array_shift($parts); // Skip text before first ##

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            if (preg_match('/^##\s+(.+?)\s*\n(.*)/us', $part, $sm)) {
                $items[] = [
                    'title' => trim($sm[1]),
                    'content' => trim($sm[2]),
                ];
            }
        }

        if (empty($items)) {
            $errors[] = "rms-v2-{$sectionName}-empty: La sección {$sectionName} no contiene sub-elementos.";
        }

        return ['items' => $items, 'errors' => $errors];
    }

    /**
     * Parse simple list sections (Conceptos, Recetas relacionadas).
     * Format:
     * - Item 1
     * - Item 2
     */
    public function validateList(string $content, string $sectionName): array
    {
        $errors = [];
        $items = [];

        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '- ')) {
                $item = trim(substr($line, 2));
                if (!empty($item)) {
                    $items[] = $item;
                }
            }
        }

        if (empty($items)) {
            $errors[] = "rms-v2-{$sectionName}-empty: La sección {$sectionName} no contiene elementos de lista.";
        }

        return ['items' => $items, 'errors' => $errors];
    }
}
