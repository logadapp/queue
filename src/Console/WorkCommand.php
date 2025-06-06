<?php

declare(strict_types=1);

namespace LogadApp\Queue\Console;

use LogadApp\Queue\Worker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'queue:work',
    description: 'Process jobs from the queue.',
    hidden: false
)]
final class WorkCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $worker = new Worker;
        $logger = function($message) use ($io) {
            $io->writeln("[" . date('Y-m-d H:i:s') . "] {$message}");
        };

        $worker->work('default', $logger);

        return Command::SUCCESS;
    }
}
