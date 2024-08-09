<?php

namespace App\Command\Hospital;

use App\Flusher;
use App\Repository\Hospital\HospitalRepository;
use App\Services\Hospital\PartnerReward;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'hospitals:rewards:recalculate',
    description: 'Пересчитать награды партнеров за госпитализацию',
    aliases: ['hospitals:re:re']
)]
class RewardsRecalculateCommand extends Command
{

    public function __construct(
        private readonly HospitalRepository $hospitals,
        private readonly PartnerReward $partnerRewards,
        private readonly Flusher $flusher,
    )
    {
        parent::__construct();
    }


    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        foreach ($this->hospitals->findAll() as $hospital) {
            $this->partnerRewards->calculate($hospital);
            $this->flusher->flush();
        }

        $io->success('Пересчитаны награды партнеров за стационар');

        return Command::SUCCESS;
    }

}
