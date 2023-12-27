<?php

namespace App\Command;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Row;
use App\Flusher;
use App\Query\PartnerReward\Fetcher;
use App\Query\PartnerReward\Query;
use App\Repository\CallingRepository;
use App\Services\YaGeolocation\Api;
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
        readonly private Api $api,
        readonly private Fetcher $fetcher,
        readonly private CallingRepository $calls,
        readonly private Flusher $flusher,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var Calling $call */
        foreach ($this->calls->findAll() as $call){
            if (count($call->getServices())){
                dump('-------------------------');
                dump($call->getPartner()?->getName());
                /** @var Row $row */
                $fullReward = 0;
                foreach ($call->getServices() as $row){
                    $query = new Query(
                        $call->getPartner()?->getId(),
                        $row->getService()?->getCategory()?->getId(),
                        0,
                        $call->getMkadDistance()
                    );

                    $percent = $this->fetcher->fetch($query);
                    $reward = (int)(($row->getPrice() - $row->getService()->getCoastPrice()) / 100 * $percent);

                    $fullReward  += $reward;
                    dump($row->getService()->getName());
                    dump($percent);
                    dump($row->getPrice());
                    dump($reward);
                    $row->setPartnerReward($reward);
                    dump('************************');
                }

                $call->setPartnerReward($fullReward);

                $this->flusher->flush();
            }
        }


       //$query = new Query(
       //    1,
       //    1,
       //    1,
       //    0
       //);

       //$this->fetcher->fetch($query);

       //$data = $this->api->getPositionByAddress('Москва Анохина 13');


      //  dd($data);

        $io->success('Test successful.');

        return Command::SUCCESS;
    }
}
