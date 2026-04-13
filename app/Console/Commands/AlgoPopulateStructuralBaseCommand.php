<?php

namespace App\Console\Commands;

use App\Services\AlgoPopulators\EntityStructuralBasePopulator;
use Illuminate\Console\Command;

/**
 * Populate entity_structural_base table
 * 
 * Usage: php artisan algo:populate-structural-base [--location=ID]
 */
class AlgoPopulateStructuralBaseCommand extends Command
{
    protected $signature = 'algo:populate-structural-base
        {--location= : Specific location ID to populate}';

    protected $description = 'Populate entity_structural_base table from Sight, Restaurant, Experience';

    public function handle(): int
    {
        $locationId = $this->option('location') ? (int)$this->option('location') : null;

        $this->info('Populating entity_structural_base...');

        $populator = new EntityStructuralBasePopulator();
        $stats = $populator->populate($locationId);

        $this->newLine();
        $this->info('Results:');
        $this->line("  Sights inserted:      {$stats['sights_inserted']}");
        $this->line("  Restaurants inserted: {$stats['restaurants_inserted']}");
        $this->line("  Experiences inserted: {$stats['experiences_inserted']}");
        $this->line("  Priors updated:       {$stats['priors_updated']}");
        $this->line("  Percentiles computed: {$stats['percentiles_computed']}");
        $this->line("  Density computed:     {$stats['density_computed']}");

        return Command::SUCCESS;
    }
}
