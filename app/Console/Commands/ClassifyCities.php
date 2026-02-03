<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class ClassifyCities extends Command
{
    protected $signature = 'classify:cities';
    protected $description = 'Run the classification algorithm for all cities';

    public function handle()
{
    $cities = DB::table('Location')
        ->where('LocationLevel', 2)   // city-level rows
        ->get();

    $service = new \App\Services\ClassificationService();

    foreach ($cities as $city) {
        $this->info("Classifying: " . $city->Name);
        $service->classifyCity($city->LocationId);
    }

    $this->info("DONE.");
}

}
