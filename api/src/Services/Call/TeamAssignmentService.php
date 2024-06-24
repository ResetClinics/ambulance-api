<?php

namespace App\Services\Call;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Flusher;
use App\Repository\MedTeam\MedTeamRepository;
use App\Services\CallingSender;
use DomainException;

class TeamAssignmentService
{
    public function __construct(
        private readonly MedTeamRepository $teams,
        private readonly Flusher $flusher,
        private readonly CallingSender $sender,
    )
    {
    }

    public function toAppoint(Calling $call, ?int $teamId): void
    {
        if (!$teamId) {
            return;
        }

        if ($call->getStatus() !== Status::ASSIGNED) {
            return;
        }

        $medTeam = $this->teams->getLastWorkByNumber($teamId);

        if (!$medTeam){
            throw new DomainException('Не найдено рабочей бригады № ' . $teamId);
        }

        if ($call->getTeam() === $medTeam) {
            return;
        }

        $call->setTeam($medTeam);
        $call->setAdmin($medTeam->getAdmin());
        $call->setDoctor($medTeam->getDoctor());

        $this->flusher->flush();

        $this->sender->sendToAdmin(
            $call,
            'Внимание новый заказ',
            $call->getAddress()
        );
    }
}