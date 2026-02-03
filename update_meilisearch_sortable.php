<?php

/**
 * Update Meilisearch sortable attributes for locations index
 * Run this on production server to fix the tourismScore sorting error
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Updating Meilisearch Sortable Attributes ===\n\n";

try {
    // Initialize Meilisearch client
    $client = new \Meilisearch\Client(
        config('scout.meilisearch.host'), 
        config('scout.meilisearch.key')
    );
    
    echo "✓ Connected to Meilisearch at " . config('scout.meilisearch.host') . "\n";
    
    // Get the locations index
    $index = $client->index('locations');
    
    // Get current sortable attributes
    echo "\nCurrent sortable attributes: " . json_encode($index->getSortableAttributes()) . "\n";
    
    // Update sortable attributes to include tourismScore
    echo "\nUpdating sortable attributes...\n";
    $task = $index->updateSortableAttributes(['id', 'name', 'tourismScore']);
    
    echo "Task UID: " . $task['taskUid'] . "\n";
    echo "Waiting for task to complete...\n";
    
    // Wait for the task to complete (max 30 seconds)
    $index->waitForTask($task['taskUid'], 30000);
    
    // Verify the configuration
    $sortableAttributes = $index->getSortableAttributes();
    echo "\n✓ Updated sortable attributes: " . json_encode($sortableAttributes) . "\n";
    
    echo "\n=== Update Complete ===\n";
    echo "The location search will now sort by tourismScore correctly.\n";
    echo "You may need to clear the cache: php artisan cache:clear\n";
    
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
