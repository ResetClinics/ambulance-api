<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MyAction extends AbstractController
{
    public function __invoke(TeamRepository $teams): User
    {
        /** @var User $user */
        $user = $this->getUser();

        return $user;
    }
}
