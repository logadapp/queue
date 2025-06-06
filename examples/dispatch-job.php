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
NewUserJob::dispatch("test@test.com", "Michael");

echo "job dispatched successfully!\n";

// Dispatch multiple jobs in a loop
for ($i = 1; $i <= 5; $i++) {
	$job = new NewUserJob(
		email: "explicit-{$i}@test.com",
		firstName: "Explicit {$i}"
	);
	$job->dispatchSelf();

    echo "Dispatched #{$i}\n";
}


var_dump(\LogadApp\Queue\Queue::list());

echo "All jobs have been added to the queue\n";
