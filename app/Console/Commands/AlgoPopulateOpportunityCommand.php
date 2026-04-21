<?php

namespace App\Console\Commands;

use App\Services\AlgoPopulators\EntityOpportunityProfilePopulator;
use Illuminate\Console\Command;

/**
 * Populate entity_opportunity_profile table
 * 
 * Usage: php artisan algo:populate-opportunity [--location=ID]
 */
class AlgoPopulateOpportunityCommand extends Command
{
    protected $signature = 'algo:populate-opportunity
        {--location= : Specific location ID to process}';

    protected $description = 'Populate entity_opportunity_profile table with opportunity scores';

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

        $locationId = $this->option('location') ? (int)$this->option('location') : null;

        $this->info('Populating opportunity profiles...');

        $populator = new EntityOpportunityProfilePopulator();
        $stats = $populator->populate($locationId);

        $this->newLine();
        $this->info('Results:');
        $this->line("  Sights processed:      {$stats['sights_processed']}");
        $this->line("  Restaurants processed: {$stats['restaurants_processed']}");
        $this->line("  Experiences processed: {$stats['experiences_processed']}");

        return Command::SUCCESS;
    }
}
