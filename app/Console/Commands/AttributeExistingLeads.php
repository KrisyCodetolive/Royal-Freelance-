<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Console\Command;

class AttributeExistingLeads extends Command
{
    protected $signature = 'leads:attribute-existing 
                            {--dry-run : Run without making changes}
                            {--funnel= : Only process leads from specific funnel ID}';

    protected $description = 'Attribute existing leads to commercials based on funnel_user configuration';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $funnelId = $this->option('funnel');

        $this->info('🔍 Searching for leads without brought_by attribution...');

        // Récupérer les leads sans brought_by
        $query = Lead::whereNull('brought_by')
            ->whereNotNull('funnel_id');

        if ($funnelId) {
            $query->where('funnel_id', $funnelId);
        }

        $leads = $query->with('funnel')->get();

        if ($leads->isEmpty()) {
            $this->info('✅ No leads found without attribution.');
            return 0;
        }

        $this->info("Found {$leads->count()} leads to process.");

        $attributed = 0;
        $skipped = 0;

        $progressBar = $this->output->createProgressBar($leads->count());
        $progressBar->start();

        foreach ($leads as $lead) {
            // Essayer de trouver un commercial pour ce funnel
            // Stratégie : prendre le premier commercial actif sur ce funnel
            $commercial = User::role('commercial')
                ->whereHas('usableFunnels', function ($query) use ($lead) {
                    $query->where('funnels.id', $lead->funnel_id)
                        ->where('funnel_user.is_active', true);
                })
                ->first();

            if ($commercial) {
                if (!$isDryRun) {
                    $lead->update(['brought_by' => $commercial->id]);

                    // Incrémenter le compteur dans funnel_user
                    \DB::table('funnel_user')
                        ->where('funnel_id', $lead->funnel_id)
                        ->where('user_id', $commercial->id)
                        ->increment('leads_count');
                }

                $attributed++;

                if ($isDryRun) {
                    $this->line("\n  Would attribute Lead #{$lead->id} ({$lead->email}) to {$commercial->name}");
                }
            } else {
                $skipped++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($isDryRun) {
            $this->warn('🔸 DRY RUN MODE - No changes were made');
        }

        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Would be attributed', $attributed],
                ['⏭️  Skipped (no commercial found)', $skipped],
                ['📊 Total processed', $leads->count()],
            ]
        );

        if (!$isDryRun && $attributed > 0) {
            $this->info("✅ Successfully attributed {$attributed} leads!");
        }

        if ($isDryRun && $attributed > 0) {
            $this->info("\n💡 Run without --dry-run to apply changes");
        }

        return 0;
    }
}
