<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use LogadApp\Queue\Examples\Jobs\NewUserJob;
// use LogadApp\Queue\Stores\FileStore;
// use LogadApp\Queue\Queue;

// Configure storage
// $fileStore = new FileStore(__DIR__ . '/storage/jobs');
// Queue::useStore($fileStore);

// dispatch a job
NewUserJob::dispatch("Michael", "test@test.com");

echo "job dispatched successfully!\n";

// Dispatch multiple jobs in a loop
for ($i = 1; $i <= 5; $i++) {
	NewUserJob::dispatch("Michael-{$i}", "test@test.com");
    
    echo "Dispatched #{$i}\n";
}

var_dump(\LogadApp\Queue\Queue::list());

echo "All jobs have been added to the queue\n";
