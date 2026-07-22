<?php

namespace App\Services\RmsV2;

/**
 * Main parser for RMS v2.0 documents.
 *
 * Parses a markdown document following the Recipe Markdown Standard v2.0
 * and returns a RmsValidationResult with either parsed data or errors.
 *
 * 100% deterministic. No heuristics, no guessing, no inference.
 */
class RmsV2Parser
{
    private YamlParser $yamlParser;
    private SectionExtractor $sectionExtractor;
    private IngredientsValidator $ingredientsValidator;
    private ProcedureValidator $procedureValidator;
    private TechnicalSummaryValidator $technicalSummaryValidator;
    private ProblemsValidator $problemsValidator;
    private SectionValidators $sectionValidators;

    public function __construct()
    {
        $this->yamlParser = new YamlParser();
        $this->sectionExtractor = new SectionExtractor();
        $this->ingredientsValidator = new IngredientsValidator();
        $this->procedureValidator = new ProcedureValidator();
        $this->technicalSummaryValidator = new TechnicalSummaryValidator();
        $this->problemsValidator = new ProblemsValidator();
        $this->sectionValidators = new SectionValidators();
    }

    /**
     * Validate-only: parse the document and check for errors.
     */
    public function validate(string $markdown): RmsValidationResult
    {
        $allErrors = [];

        // 1. Parse YAML header
        $yamlResult = $this->yamlParser->parse($markdown);
        $allErrors = array_merge($allErrors, $yamlResult['errors']);

        // 2. Extract sections
        $sectionsResult = $this->sectionExtractor->extract($markdown);
        $allErrors = array_merge($allErrors, $sectionsResult['errors']);

        if (!empty($allErrors)) {
            return RmsValidationResult::failure($allErrors);
        }

        $yaml = $yamlResult['data'];
        $sections = $sectionsResult['sections'];

        // Track per-section errors
        $sectionErrors = [];

        // 3. Validate each section
        $objectiveResult = $this->sectionValidators->validateText(
            $sections['objetivo'] ?? '', 'objetivo'
        );
        $sectionErrors = array_merge($sectionErrors, $objectiveResult['errors']);

        $ingredientsResult = $this->ingredientsValidator->validate(
            $sections['ingredientes'] ?? ''
        );
        $sectionErrors = array_merge($sectionErrors, $ingredientsResult['errors']);

        $equipmentResult = $this->sectionValidators->validateList(
            $sections['equipo'] ?? '', 'equipo'
        );
        $sectionErrors = array_merge($sectionErrors, $equipmentResult['errors']);

        $prepResult = $this->sectionValidators->validateText(
            $sections['preparacion_previa'] ?? '', 'preparacion-previa'
        );

        $procedureResult = $this->procedureValidator->validate(
            $sections['procedimiento'] ?? ''
        );
        $sectionErrors = array_merge($sectionErrors, $procedureResult['errors']);

        $resultadoResult = $this->sectionValidators->validateH2Section(
            $sections['resultado_esperado'] ?? '', 'resultado-esperado'
        );

        $variantsResult = $this->sectionValidators->validateH2Section(
            $sections['variantes'] ?? '', 'variantes'
        );

        $adaptationsResult = $this->sectionValidators->validateH2Section(
            $sections['adaptaciones'] ?? '', 'adaptaciones'
        );

        $conservacionText = $sections['conservacion'] ?? '';
        $conservacionResult = ['text' => trim($conservacionText), 'errors' => []];
        if (empty($conservacionResult['text'])) {
            $sectionErrors[] = 'rms-v2-conservacion-empty: La sección Conservación está vacía.';
        }

        $technicalResult = $this->technicalSummaryValidator->validate(
            $sections['resumen_tecnico'] ?? ''
        );
        $sectionErrors = array_merge($sectionErrors, $technicalResult['errors']);

        $conceptsResult = $this->sectionValidators->validateList(
            $sections['conceptos_aprendidos'] ?? '', 'conceptos-aprendidos'
        );
        $sectionErrors = array_merge($sectionErrors, $conceptsResult['errors']);

        $problemsResult = $this->problemsValidator->validate(
            $sections['problemas_frecuentes'] ?? ''
        );
        $sectionErrors = array_merge($sectionErrors, $problemsResult['errors']);

        $notesResult = $this->sectionValidators->validateText(
            $sections['notas_tecnicas'] ?? '', 'notas-tecnicas'
        );

        $relatedResult = $this->sectionValidators->validateList(
            $sections['recetas_relacionadas'] ?? '', 'recetas-relacionadas'
        );

        $whyResult = ['text' => $sections['por_que_funciona'] ?? '', 'errors' => []];

        // Collect all errors
        $allErrors = array_merge($allErrors, $sectionErrors);

        if (!empty($allErrors)) {
            return RmsValidationResult::failure($allErrors);
        }

        // Build result data
        $data = [
            'name' => $yaml['title'],
            'slug' => $yaml['slug'],
            'recipe_type' => $yaml['recipe_type'] ?? 'base',
            'description' => $yaml['title'],
            'objective' => $objectiveResult['text'] ?? '',
            'why_it_works' => $whyResult['text'] ?? '',
            'prep_time' => $yaml['prep_time'] ?? 0,
            'cook_time' => $yaml['cook_time'] ?? 0,
            'total_time' => $yaml['total_time'] ?? 0,
            'servings' => $yaml['servings'] ?? 4,
            'difficulty' => $yaml['difficulty'] ?? 1,
            'cost' => $yaml['cost'] ?? 'Medio',
            'category' => $yaml['category'],
            'family' => $yaml['family'] ?? $yaml['category'],
            'tags' => $yaml['tags'] ?? [],
            'dependencies' => $yaml['dependencies'] ?? [],
            'author' => $yaml['author'] ?? 'Recetario',
            'language' => $yaml['language'] ?? 'es-MX',
            'ingredients' => $ingredientsResult['ingredients'] ?? [],
            'equipment' => $equipmentResult['items'] ?? [],
            'preparation' => $prepResult['text'] ?? '',
            'steps' => $procedureResult['steps'] ?? [],
            'results' => $resultadoResult['items'] ?? [],
            'variants' => $variantsResult['items'] ?? [],
            'adaptations' => $adaptationsResult['items'] ?? [],
            'storage' => $conservacionResult['text'] ?? '',
            'technical' => $technicalResult['summary'] ?? [],
            'concepts' => $conceptsResult['items'] ?? [],
            'errors_list' => $problemsResult['errors_list'] ?? [],
            'chef_notes' => $notesResult['text'] ?? '',
            'related_recipes' => $relatedResult['items'] ?? [],
            'pressure_release' => $yaml['release'] ?? null,
        ];

        return RmsValidationResult::success($data);
    }
}
