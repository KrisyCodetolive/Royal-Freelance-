<?php

namespace Database\Seeders;

use App\Services\TemplateGeneratorService;
use Illuminate\Database\Seeder;

class FunnelTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $service = new TemplateGeneratorService();

        $this->command->info('Generating default funnel templates...');

        $templates = $service->generateAllDefaults();

        foreach ($templates as $template) {
            $pagesCount = $template->pages()->count();
            $blocksCount = $template->pages()->withCount('blocks')->get()->sum('blocks_count');

            $this->command->line("  ✓ {$template->name} ({$template->template_category}) - {$pagesCount} pages, {$blocksCount} blocks");
        }

        $this->command->info('✅ ' . count($templates) . ' templates created successfully!');
    }
}
