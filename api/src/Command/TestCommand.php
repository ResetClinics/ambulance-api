<?php

namespace App\Command;

use App\Entity\Calling\Status;
use App\Flusher;
use App\Query\PartnerReward\Fetcher;
use App\Query\PartnerReward\Query;
use App\Repository\CallingRepository;
use App\Services\Call\PartnerReward;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Amp\Iterator\concat;

#[AsCommand(
    name: 'app:test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    public function __construct(
        readonly private PartnerReward $partnerReward,
        readonly private CallingRepository $callings,
        readonly private Flusher $flusher
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        ini_set('memory_limit', '-1');

        foreach ($this->callings->findAll() as $calling){

            if ($calling->getStatus() !== Status::COMPLETED){
                continue;
            }

            $this->partnerReward->calculate($calling);
            $this->flusher->flush();
        }

        $io->success('Test successful.');

        return Command::SUCCESS;
    }
}
