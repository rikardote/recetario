<?php

namespace App\Services\RmsV2;

/**
 * Validates the problems section.
 *
 * Format:
 * ## La carne quedó dura
 * ### Causa
 * Texto
 * ### Solución
 * Texto
 */
class ProblemsValidator
{
    /**
     * Parse problems.
     * Returns ['errors_list' => [...], 'errors' => []].
     */
    public function validate(string $content): array
    {
        $validationErrors = [];
        $problems = [];

        // Prepend newline for content starting with ## Problem
        $content = "\n" . $content;
        $parts = preg_split('/\n(?=##\s)/u', $content);
        array_shift($parts); // Skip text before first problem

        if (empty($parts)) {
            $validationErrors[] = 'rms-v2-problems-empty: La sección Problemas frecuentes no contiene ningún problema.';
            return ['errors_list' => $problems, 'errors' => $validationErrors];
        }

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            if (!preg_match('/^##\s+(.+?)\s*\n(.*)/us', $part, $sm)) {
                continue;
            }

            $problem = trim($sm[1]);
            $body = $sm[2];

            $cause = null;
            $solution = '';

            // Extract ### Causa
            if (preg_match('/###\s+Causa\s*\n(.*?)(?=\n###\s+|$)/us', $body, $cm)) {
                $cause = trim($cm[1]);
            }

            // Extract ### Solución
            if (preg_match('/###\s+Soluci[óo]n\s*\n(.*?)(?=\n###\s+|$)/us', $body, $sm2)) {
                $solution = trim($sm2[1]);
            }

            if (empty($cause) && empty($solution)) {
                $validationErrors[] = "rms-v2-problems-missing-blocks: El problema «{$problem}» no tiene Causa ni Solución.";
            }

            $problems[] = [
                'problem' => $problem,
                'possible_cause' => $cause,
                'solution' => $solution,
            ];
        }

        return ['errors_list' => $problems, 'errors' => $validationErrors];
    }
}
