<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Entity\User;
use App\Repository\TeamRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AdministratorAction extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(TeamRepository $teams): array
    {
        /** @var User $user */
        $user = $this->getUser();
        $team = $teams->getActiveByAdministrator($user);
        return [
            'administrator' => [
                'id' => $team->getAdministrator()->getId(),
                'name' => $team->getAdministrator()->getName(),
                'position' => $team->getAdministrator()->getPosition(),
            ],
            'doctors' => array_map(static function (User $user) {
                return [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'position' => $user->getPosition(),
                ];
            }, $team->getDoctors()),
            'status' => $team->getStatus(),
            'createdAt' => $team->getCreatedAt()->format('d.m.Y H:i'),
        ];
    }
}
