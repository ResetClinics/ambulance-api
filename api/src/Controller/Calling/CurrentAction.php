<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Entity\Calling\Calling;
use App\Entity\User\User;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CurrentAction extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(TeamRepository $teams, CallingRepository $callings): Calling
    {
        /** @var User $user */
        $user = $this->getUser();
        $team = $teams->getActiveByAdministrator($user);
        return $callings->getCurrentByTeam($team);
    }
}
