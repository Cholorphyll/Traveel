<?php

namespace App\Console\Commands;

use App\Services\AlgoPopulators\EntityStructuralImportancePopulator;
use Illuminate\Console\Command;

/**
 * Compute structural importance scores
 * 
 * Usage: php artisan algo:populate-importance [--location=ID]
 */
class AlgoPopulateImportanceCommand extends Command
{
    protected $signature = 'algo:populate-importance
        {--location= : Specific location ID to process}';

    protected $description = 'Compute and store structural importance scores for all entities';

    public function handle(): int
    {
        $locationId = $this->option('location') ? (int)$this->option('location') : null;

        $this->info('Computing structural importance scores...');

        $populator = new EntityStructuralImportancePopulator();
        $stats = $populator->populate($locationId);

        $this->newLine();
        $this->info('Results:');
        $this->line("  Intrinsic scores computed:  {$stats['intrinsic_computed']}");
        $this->line("  Relational scores computed: {$stats['relational_computed']}");
        $this->line("  Final scores computed:      {$stats['final_scores_computed']}");
        $this->line("  Classes assigned:           {$stats['classes_assigned']}");

        return Command::SUCCESS;
    }
}
