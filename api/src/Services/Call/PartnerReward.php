<?php

namespace App\Services\Call;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Query\PartnerReward\Fetcher;
use App\Query\PartnerReward\Query;

class PartnerReward
{
    public function __construct(
        readonly private Fetcher $fetcher,
    )
    {
    }

    public function calculate(Calling $call): void
    {
        $fullReward = 0;
        if ($call->getStatus() !== Status::COMPLETED){
            return;
        }

        foreach ($call->getServices() as $row){

            $query = new Query(
                $call->getCompletedAt(),
                $call->getPartner()?->getId(),
                $row->getService()?->getCategory()?->getId(),
                $call->getCountRepeat(),
                $call->getMkadDistance() === null ? 0 : $call->getMkadDistance()
            );

            $percent = $this->fetcher->fetch($query);

            $reward = (int)(($row->getPrice() - $row->getService()->getCoastPrice()) / 100 * $percent);

            $fullReward  += $reward;

            $row->setPartnerReward($reward);
        }

        $call->setPartnerReward($fullReward);
    }
}