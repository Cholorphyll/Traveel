<?php

namespace App\Console\Commands;

use App\Services\AlgoPopulators\EntityStructuralBasePopulator;
use App\Services\AlgoPopulators\DestinationClusterBuilder;
use App\Services\AlgoPopulators\EntityStructuralImportancePopulator;
use App\Services\AlgoPopulators\EntityOpportunityProfilePopulator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * AlgoPopulateCommand
 * 
 * Master command to populate all algo engine tables.
 * Runs in the correct order as specified in the design docs.
 * 
 * Usage:
 *   php artisan algo:populate                    # Populate all locations
 *   php artisan:populate --location=123         # Populate specific location
 *   php artisan:populate --phase=1              # Run only phase 1
 */
class AlgoPopulateCommand extends Command
{
    protected $signature = 'algo:populate
        {--location= : Specific location ID to populate}
        {--phase= : Run only specific phase (1-4)}
        {--start-phase= : Start from this phase (1-4), skip earlier phases}
        {--force : Force repopulate even if data exists}';

    protected $description = 'Populate all algo engine tables from source data (Sight, Restaurant, Experience)';

    protected int $locationId;

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

        $this->info('=== Algo Engine Table Population ===');
        $this->newLine();

        $this->locationId = (int)$this->option('location');
        $phase = (int)$this->option('phase');
        $startPhase = (int)$this->option('start-phase') ?: 1;
        $force = $this->option('force');

        if ($this->locationId) {
            $this->info("Processing location ID: {$this->locationId}");
        } else {
            $this->info('Processing ALL locations');
        }

        if ($startPhase > 1) {
            $this->warn("Starting from Phase {$startPhase} (skipping earlier phases)");
        }

        $this->newLine();

        // Check if tables exist
        $this->checkTablesExist();

        // Run phases in order (respecting start-phase)
        if ((!$phase || $phase === 1) && $startPhase <= 1) {
            $this->runPhase1();
        }

        if ((!$phase || $phase === 2) && $startPhase <= 2) {
            $this->runPhase2();
        }

        if ((!$phase || $phase === 3) && $startPhase <= 3) {
            $this->runPhase3();
        }

        if ((!$phase || $phase === 4) && $startPhase <= 4) {
            $this->runPhase4();
        }

        $this->newLine();
        $this->info('=== Population Complete ===');

        return Command::SUCCESS;
    }

    /**
     * Check that required tables exist
     */
    protected function checkTablesExist(): void
    {
        $tables = [
            'entity_structural_base',
            'category_structural_priors',
            'destination_clusters',
            'destination_cluster_members',
            'entity_structural_importance',
            'entity_opportunity_profile',
        ];

        $missing = [];

        foreach ($tables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $missing[] = $table;
            }
        }

        if (!empty($missing)) {
            $this->error('Missing tables: ' . implode(', ', $missing));
            $this->error('Please run: php artisan migrate');
            exit(1);
        }

        $this->info('All required tables exist.');
    }

    /**
     * PHASE 1: Build entity_structural_base
     */
    protected function runPhase1(): void
    {
        $this->info('--- Phase 1: Entity Structural Base ---');

        $skipTruncate = (int)$this->option('start-phase') > 1;

        $populator = new EntityStructuralBasePopulator();
        $populator->setProgressCallback(function (string $message) {
            $this->line("  " . $message);
        });

        if ($skipTruncate) {
            $populator->skipTruncate(true);
        }

        $stats = $populator->populate($this->locationId ?: null);

        $this->newLine();
        $this->line("  <info>Summary:</info>");
        $this->line("  Sights inserted:      {$stats['sights_inserted']}");
        $this->line("  Restaurants inserted: {$stats['restaurants_inserted']}");
        $this->line("  Experiences inserted: {$stats['experiences_inserted']}");
        $this->line("  Priors updated:       {$stats['priors_updated']}");
        $this->line("  Percentiles computed: {$stats['percentiles_computed']}");
        $this->line("  Density computed:     {$stats['density_computed']}");

        $this->newLine();
    }

    /**
     * PHASE 2: Build destination clusters
     */
    protected function runPhase2(): void
    {
        $this->info('--- Phase 2: Destination Clusters ---');

        $skipTruncate = (int)$this->option('start-phase') > 2;

        $builder = new DestinationClusterBuilder();
        $builder->setProgressCallback(function (string $message) {
            $this->line("  " . $message);
        });

        if ($skipTruncate) {
            $builder->skipTruncate(true);
        }

        $stats = $builder->build($this->locationId ?: null);

        $this->newLine();
        $this->line("  <info>Summary:</info>");
        $this->line("  Clusters created:   {$stats['clusters_created']}");
        $this->line("  Entities assigned:  {$stats['entities_assigned']}");
        $this->line("  Noise points:       {$stats['noise_points']}");

        $this->newLine();
    }

    /**
     * PHASE 3: Compute structural importance scores
     */
    protected function runPhase3(): void
    {
        $this->info('--- Phase 3: Structural Importance Scores ---');

        $populator = new EntityStructuralImportancePopulator();
        $populator->setProgressCallback(function (string $message) {
            $this->line("  " . $message);
        });

        $stats = $populator->populate($this->locationId ?: null);

        $this->newLine();
        $this->line("  <info>Summary:</info>");
        $this->line("  Intrinsic scores computed:  {$stats['intrinsic_computed']}");
        $this->line("  Relational scores computed: {$stats['relational_computed']}");
        $this->line("  Final scores computed:      {$stats['final_scores_computed']}");
        $this->line("  Classes assigned:           {$stats['classes_assigned']}");

        $this->newLine();
    }

    /**
     * PHASE 4: Build opportunity profiles
     */
    protected function runPhase4(): void
    {
        $this->info('--- Phase 4: Opportunity Profiles ---');

        $skipTruncate = (int)$this->option('start-phase') > 4;

        $populator = new EntityOpportunityProfilePopulator();
        $populator->setProgressCallback(function (string $message) {
            $this->line("  " . $message);
        });

        if ($skipTruncate) {
            $populator->skipTruncate(true);
        }

        $stats = $populator->populate($this->locationId ?: null);

        $this->newLine();
        $this->line("  <info>Summary:</info>");
        $this->line("  Sights processed:      {$stats['sights_processed']}");
        $this->line("  Restaurants processed: {$stats['restaurants_processed']}");
        $this->line("  Experiences processed: {$stats['experiences_processed']}");

        $this->newLine();
    }
}
