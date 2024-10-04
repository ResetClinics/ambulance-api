<?php

namespace App\Services\MedTeam;

use App\Entity\MedTeam\MedTeam;
use App\Services\SmsSender\McnSmsSender;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class EmployeeNotification
{
    public function __construct(
        readonly private McnSmsSender $sender
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(MedTeam $medTeam): void
    {
        $message = $medTeam->getPlannedStartAt()->format('d.m.Y') . '%0D%0A';
        $message .= $medTeam->getPlannedStartAt()->format('H:i') . ' - ' .  $medTeam->getPlannedFinishAt()->format('H:i') . '%0D%0A';

        if ($medTeam->getDoctor()){
            $message .= 'В: ' . $medTeam->getDoctor()->getName() . '%0D%0A';
        }

        if ($medTeam->getAdmin()){
            $message .= 'А: ' . $medTeam->getAdmin()->getName() . '%0D%0A';
        }

        if ($medTeam->getDriver()){
            $message .= 'Ш: ' . $medTeam->getDriver()->getName() . '%0D%0A';
        }

        if ($medTeam->getBase()){
            $message .= 'База: ' . $medTeam->getBase()->getName() . '%0D%0A';
        }

        if ($medTeam->getCar()){
            $message .= 'Авто: ' . $medTeam->getCar()->getName() . '%0D%0A';
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

        if ($medTeam->getDriver()){
            $this->sender->send(
                $medTeam->getDriver()->getPhone(),
                $message
            );
        }
    }
}