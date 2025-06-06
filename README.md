# LogadApp\Queue - Coming Soon

A lightweight (low-budget), queue system for PHP applications.

### Why??
1. Because I can.
2. I was working on some vanilla php projects and sometimes needed to create cron jobs for each operation I wanted to run in the background. But I'd have to create a dedicated file to run the job, add a new entry in crontab..... 

Checkout other projects under logadapp 😊
## Features


## Installation (Coming soon)

```bash
composer require 
```

## Basic Usage

### Creating a Job

Create a job by extending the base `Job` class:

```php
<?php

namespace App\Jobs;

use LogadApp\Queue\Job;

class SendEmailJob extends Job
{
    public function __construct(
        private readonly string $recipient,
        private readonly string $subject,
        private readonly string $content
    ) {}

    protected function handle(): void
    {
        // Email sending logic here
    }
}
```

### Dispatching Jobs

```php
<?php

use App\Jobs\SendEmailJob;

// Dispatch a job to the default queue
SendEmailJob::dispatch(
    'user@example.com',
    'Welcome!',
    'Thanks for signing up.'
));

// extra parenthesis because 8.5 is not out yet
(new SendEmailJob(
  recipient: "explicit-{$i}@test.com", 
  subject: "Explicit {$i}",
  content: "HII"
))->dispatchSelf();
```

### Processing Jobs

Run the worker command to process jobs:

```bash
php bin/console queue:work
```

## Configuration

## Coming Soon

- Database storage support
- Failed job handling