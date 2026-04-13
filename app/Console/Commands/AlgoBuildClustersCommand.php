<?php

namespace App\Console\Commands;

use App\Services\AlgoPopulators\DestinationClusterBuilder;
use Illuminate\Console\Command;

/**
 * Build destination clusters using DBSCAN
 * 
 * Usage: php artisan algo:build-clusters [--location=ID]
 */
class AlgoBuildClustersCommand extends Command
{
    protected $signature = 'algo:build-clusters
        {--location= : Specific location ID to process}
        {--epsilon=0.0045 : DBSCAN epsilon parameter (degrees)}
        {--min-points=4 : DBSCAN minimum points parameter}';

    protected $description = 'Build destination clusters using DBSCAN algorithm';

    public function handle(): int
    {
        $locationId = $this->option('location') ? (int)$this->option('location') : null;

        $this->info('Building destination clusters...');

        $builder = new DestinationClusterBuilder();

        // Apply custom parameters if provided
        if ($this->option('epsilon')) {
            $builder->setEpsilon((float)$this->option('epsilon'));
        }
        if ($this->option('min-points')) {
            $builder->setMinPoints((int)$this->option('min-points'));
        }

        $stats = $builder->build($locationId);

        $this->newLine();
        $this->info('Results:');
        $this->line("  Clusters created:   {$stats['clusters_created']}");
        $this->line("  Entities assigned:  {$stats['entities_assigned']}");
        $this->line("  Noise points:       {$stats['noise_points']}");

        return Command::SUCCESS;
    }
}
