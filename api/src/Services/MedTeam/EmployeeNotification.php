<?php

namespace App\Services\MedTeam;

use App\Entity\MedTeam\MedTeam;
use App\Services\SmsSender\SmsSender;

class EmployeeNotification
{
    public function __construct(
        readonly private SmsSender $sender
    )
    {
    }

    public function send(MedTeam $medTeam): void
    {
        $message = $medTeam->getPlannedStartAt()->format('d.m.Y H:i') . '
';
        $message .= $medTeam->getPlannedStartAt()->format('H:i') . ' - ' .  $medTeam->getPlannedFinishAt()->format('H:i') . '
';

        if ($medTeam->getPhone()){
            $message .= 'Бригада № ' . $medTeam->getPhone()->getId() . '
';
        }

        if ($medTeam->getDoctor()){
            $message .= 'В: ' . $medTeam->getDoctor()->getName() . '
';
        }

        if ($medTeam->getAdmin()){
            $message .= 'А: ' . $medTeam->getAdmin()->getName() . '
';
        }

        if ($medTeam->getCar()){
            $message .= 'Авто: ' . $medTeam->getCar()->getName() . '
';
        }

        if ($medTeam->getAdmin()){
            $this->sender->send(
                $medTeam->getAdmin()->getPhone(),
                $message
            );
        }

        if ($medTeam->getDoctor()){
            $this->sender->send(
                $medTeam->getDoctor()->getPhone(),
                $message
            );
        }
    }
}