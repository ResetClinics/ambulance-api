<?php

namespace App\Console;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\MkadDistance\Distance;
use App\Repository\CallingRepository;
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
        private readonly CallingRepository $calls,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');
        $totalCount = 0;
        $totalAmount = 0;
        $repeat1Count = 0;

        $repeat1Amount = 0;
        $repeat2Count = 0;
        $repeat2Amount = 0;
        $repeat3Count = 0;
        $repeat3Amount = 0;

        /** @var Calling $call */
        foreach ($this->calls->findAllByStatus(Status::COMPLETED) as $call) {

            $totalCount++;
            $totalAmount += $call->getPrice();

            if ($call->getCountRepeat() === 1) {
                $repeat1Count++;
                $repeat1Amount += $call->getPrice();
            }

            if ($call->getCountRepeat() === 2) {
                $repeat2Count++;
                $repeat2Amount += $call->getPrice();
            }

            if ($call->getCountRepeat() === 3) {
                $repeat3Count++;
                $repeat3Amount += $call->getPrice();
            }

        }

        echo 'Вызовы' . PHP_EOL;
        echo $totalCount . PHP_EOL;
        echo $totalAmount . PHP_EOL;

        echo 'Повтор1' . PHP_EOL;
        echo $repeat1Count . PHP_EOL;
        echo $repeat1Amount . PHP_EOL;

        echo 'Повтор2' . PHP_EOL;
        echo $repeat2Count . PHP_EOL;
        echo $repeat2Amount . PHP_EOL;

        echo 'Повтор3' . PHP_EOL;
        echo $repeat3Count . PHP_EOL;
        echo $repeat3Amount . PHP_EOL;

        return Command::SUCCESS;
    }
}

