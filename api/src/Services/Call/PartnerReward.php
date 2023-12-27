<?php

namespace App\Services\Call;

use App\Entity\Calling\Calling;
use App\Flusher;
use App\Query\PartnerReward\Fetcher;
use App\Query\PartnerReward\Query;

class PartnerReward
{
    public function __construct(
        readonly private Fetcher $fetcher,
        readonly private Flusher $flusher,
    )
    {
    }

    public function calculate(Calling $call): void
    {
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