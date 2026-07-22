<?php

namespace App\Services\RmsV2;

/**
 * Validates the procedure section.
 *
 * Each step must have exactly these 4 sub-blocks:
 * ### Acción
 * ### Fundamento técnico
 * ### Qué observar
 * ### Error común
 */
class ProcedureValidator
{
    private const REQUIRED_BLOCKS = [
        'acción' => 'Acción',
        'fundamento_tecnico' => 'Fundamento técnico',
        'que_observar' => 'Qué observar',
        'error_comun' => 'Error común',
    ];

    /**
     * Parse procedure steps.
     * Returns ['steps' => [...], 'errors' => []].
     */
    public function validate(string $content): array
    {
        $errors = [];
        $steps = [];

        // Split by ## Paso N
        $parts = preg_split('/\n(?=##\s+Paso\s+\d)/u', $content);
        // Skip text before first step
        array_shift($parts);

        if (empty($parts)) {
            $errors[] = 'rms-v2-procedure-empty: La sección Procedimiento no contiene ningún paso.';
            return ['steps' => $steps, 'errors' => $errors];
        }

        foreach ($parts as $part) {
            $stepErrors = $this->validateStep($part);
            if (!empty($stepErrors['errors'])) {
                $stepNumber = count($steps) + 1;
                foreach ($stepErrors['errors'] as $e) {
                    $errors[] = "rms-v2-procedure-step-{$stepNumber}: {$e}";
                }
            }
            $steps[] = $stepErrors['data'];
        }

        return ['steps' => $steps, 'errors' => $errors];
    }

    private function validateStep(string $block): array
    {
        $data = [
            'action' => '',
            'technical_fundament' => '',
            'what_to_observe' => null,
            'common_errors' => null,
        ];
        $errors = [];

        // Split by ### sub-headings
        $sections = preg_split('/\n(?=###\s)/u', $block);

        $foundBlocks = [];

        foreach ($sections as $sec) {
            $sec = trim($sec);
            if (empty($sec)) continue;

            // Check if this is a ### heading
            if (!preg_match('/^###\s+(.+?)\s*\n(.*)/us', $sec, $sm)) {
                // Content before any ### — might be part of action
                if (empty($foundBlocks)) {
                    $data['action'] = trim(preg_replace('/^##\s+Paso\s+\d+\n?/u', '', $sec));
                }
                continue;
            }

            $heading = trim($sm[1]);
            $content = trim($sm[2]);

            $matched = false;
            foreach (self::REQUIRED_BLOCKS as $blockKey => $blockName) {
                if (mb_strtolower($heading) === mb_strtolower($blockName)) {
                    $foundBlocks[] = $blockKey;

                    match ($blockKey) {
                        'acción' => $data['action'] = $content,
                        'fundamento_tecnico' => $data['technical_fundament'] = $content,
                        'que_observar' => $data['what_to_observe'] = $content ?: null,
                        'error_comun' => $data['common_errors'] = $content ?: null,
                        default => null,
                    };
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $errors[] = "Sub-bloque no reconocido en paso: «### {$heading}». Bloques esperados: Acción, Fundamento técnico, Qué observar, Error común.";
            }
        }

        // Check all 4 blocks are present
        foreach (self::REQUIRED_BLOCKS as $blockKey => $blockName) {
            if (!in_array($blockKey, $foundBlocks)) {
                $errors[] = "Falta el bloque «### {$blockName}».";
            }
        }

        return ['data' => $data, 'errors' => $errors];
    }
}
