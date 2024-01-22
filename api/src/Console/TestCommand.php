<?php

namespace App\Console;

use App\MkadDistance\Distance;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'test:test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    public function __construct(
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $distance = Distance::createMoscowMkadCalculator(
            [55.56923352424549,37.19087962109375]
        )->calculate();

        return Command::SUCCESS;
    }
}

