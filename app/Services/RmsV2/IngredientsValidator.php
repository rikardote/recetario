<?php

namespace App\Services\RmsV2;

/**
 * Validates the ingredients section.
 *
 * Format:
 * ## Proteína
 * - 1 kg pechuga de pollo
 * ## Verduras
 * - 2 papas
 * - 1 cebolla
 */
class IngredientsValidator
{
    /**
     * Parse ingredients.
     * Returns ['ingredients' => [...], 'errors' => []].
     */
    public function validate(string $content): array
    {
        $errors = [];
        $ingredients = [];

        // Split by H2 sub-headings
        $parts = preg_split('/\n(?=##\s)/u', $content);

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            // Extract H2 category
            if (!preg_match('/^##\s+(.+?)\s*\n(.*)/us', $part, $cm)) {
                continue;
            }

            $category = trim($cm[1]);
            $body = $cm[2];

            // Parse list items: "- 1 kg pechuga de pollo" or "- sal"
            $lines = explode("\n", $body);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                if (!str_starts_with($line, '- ')) continue;

                $desc = trim(substr($line, 2));
                if (empty($desc)) continue;

                // Parse "1 kg pechuga de pollo" into quantity, unit, name
                [$quantity, $unit, $name] = $this->parseIngredientLine($desc);

                $ingredients[] = [
                    'category' => $this->normalizeCategory($category),
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'name' => $name,
                    'description' => $desc,
                ];
            }
        }

        return ['ingredients' => $ingredients, 'errors' => $errors];
    }

    /**
     * Parse a line like "1 kg pechuga de pollo" into [quantity, unit, name].
     */
    private function parseIngredientLine(string $line): array
    {
        // Try numeric quantity first: "1 kg pechuga de pollo"
        if (preg_match('/^([\d.,\/½¼¾⅓⅔]+)\s+(\S+)\s+(.+)$/u', $line, $m)) {
            return [$this->parseNumber($m[1]), $m[2], trim($m[3])];
        }

        // Try fraction-only: "½ taza crema"
        if (preg_match('/^([½¼¾⅓⅔])\s+(\S+)\s+(.+)$/u', $line, $m)) {
            return [$this->parseNumber($m[1]), $m[2], trim($m[3])];
        }

        // No quantity: "sal", "pimienta"
        return [null, null, $line];
    }

    private function parseNumber(string $s): float
    {
        return match ($s) {
            '½' => 0.5, '¼' => 0.25, '¾' => 0.75, '⅓' => 0.33, '⅔' => 0.67,
            default => (float) str_replace(',', '.', $s),
        };
    }

    private function normalizeCategory(string $cat): string
    {
        $lower = mb_strtolower($cat);

        return match (true) {
            str_contains($lower, 'proteína') || str_contains($lower, 'proteina') => 'proteinas',
            str_contains($lower, 'verdura') => 'verduras',
            str_contains($lower, 'líquido') || str_contains($lower, 'liquido') => 'liquidos',
            str_contains($lower, 'condimento') => 'condimentos',
            str_contains($lower, 'terminación') || str_contains($lower, 'terminacion') => 'terminacion',
            default => $cat,
        };
    }
}
