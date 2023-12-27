<?php

namespace App\Command;

use App\Repository\CallingRepository;
use App\Services\Call\PartnerReward;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    public function __construct(
        readonly private CallingRepository $calls,
        readonly private PartnerReward $partnerReward,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->calls->findAll() as $call){
            if (count($call->getServices())){
                dump('-------------------------');
                dump($call->getPartner()?->getName());
               $this->partnerReward->calculate($call);
            }
        }

        $io->success('Test successful.');

        return Command::SUCCESS;
    }
}
