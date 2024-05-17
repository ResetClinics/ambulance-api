<?php

namespace App\Services\Call;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Repository\PaymentSetting\PaymentSettingRepository;

class OperatorReward
{

    public function __construct(
        private readonly PaymentSettingRepository $paymentSettings
    )
    {
    }

    public function calculate(Calling $call): void
    {
        $fullReward = 0;
        if ($call->getStatus() !== Status::COMPLETED){
            return;
        }

        $therapy = 0;
        $hospital = 0;
        $coding = 0;
        $stationary = 0;

        foreach ($call->getServices() as $row){

            if ($row->getService()->getType() === 'default'){


                if ($row->getService()->getCategory()->getId() === 1){

                }

                $hospital += $this->paymentSettings->getOperatorPercentHospital();
            }

        }
    }

}