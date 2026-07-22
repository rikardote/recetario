<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Services\RmsV2\RmsV2Importer;
use App\Services\RmsV2\RmsV2Parser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Seeds recipes from .rms.md files located in the recipes/ directory.
 *
 * Each file must follow the RMS v2.0 specification.
 * Files that fail validation are skipped with an error message.
 */
class RecipesFromMarkdownSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if recipes already exist
        if (\App\Models\Recipe::count() > 0) {
            $this->command?->info('Recipes already seeded. Skipping.');
            return;
        }
        $dir = base_path('recipes');
        if (!is_dir($dir)) {
            $this->command?->warn("Directory 'recipes/' not found. Run 'php artisan rms:export' first.");
            return;
        }

        $files = File::glob($dir . '/*.rms.md');
        if (empty($files)) {
            $this->command?->warn("No .rms.md files found in recipes/.");
            return;
        }

        $parser = new RmsV2Parser();
        $importer = new RmsV2Importer();

        $imported = 0;
        $skipped = 0;

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $markdown = File::get($filePath);

            $this->command?->info("Processing: {$filename}...");

            $result = $parser->validate($markdown);

            if (!$result->valid) {
                $this->command?->error("  ✕ {$filename}: validation failed:");
                foreach ($result->errors as $error) {
                    $this->command?->error("    - {$error}");
                }
                $skipped++;
                continue;
            }

            try {
                $importer->import($result->data, $markdown);
                $this->command?->info("  ✓ {$filename}: imported successfully.");
                $imported++;
            } catch (\Exception $e) {
                $this->command?->error("  ✕ {$filename}: import error - {$e->getMessage()}");
                $skipped++;
            }
        }

        $this->command?->info("\nDone: {$imported} imported, {$skipped} skipped.");
    }
}
