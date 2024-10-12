<?php

namespace App\Services\MedTeam;

use App\Entity\MedTeam\MedTeam;
use App\Services\TelegramSender;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class EmployeeNotification
{
    public function __construct(
        readonly private TelegramSender $sender
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(MedTeam $medTeam): void
    {
        $message = $medTeam->getPlannedStartAt()->format('d.m.Y') . '\n';
        $message .= $medTeam->getPlannedStartAt()->format('H:i') . ' - ' .  $medTeam->getPlannedFinishAt()->format('H:i') . '\n';

        if ($medTeam->getDoctor()){
            $message .= 'В: ' . $medTeam->getDoctor()->getName() . '\n';
        }

        if ($medTeam->getAdmin()){
            $message .= 'А: ' . $medTeam->getAdmin()->getName() . '\n';
        }

        if ($medTeam->getDriver()){
            $message .= 'Ш: ' . $medTeam->getDriver()->getName() . '\n';
        }

        if ($medTeam->getBase()){
            $message .= 'База: ' . $medTeam->getBase()->getName() . '\n';
        }

        if ($medTeam->getCar()){
            $message .= 'Авто: ' . $medTeam->getCar()->getName() . '\n';
        }

        if ($medTeam->getAdmin()){
            $this->sender->send(
                $medTeam->getAdmin(),
                $message
            );
        }

        if ($medTeam->getDoctor()){
            $this->sender->send(
                $medTeam->getDoctor(),
                $message
            );
        }

        if ($medTeam->getDriver()){
            $this->sender->send(
                $medTeam->getDriver(),
                $message
            );
        }
    }
}