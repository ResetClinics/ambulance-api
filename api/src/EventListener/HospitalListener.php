<?php

namespace App\EventListener;

use App\Entity\Hospital\Hospital;
use Doctrine\ORM\Event\PostUpdateEventArgs;

class HospitalListener
{
    public function postUpdate(Hospital $hospital, PostUpdateEventArgs $args): void
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $hospital->setAmount($hospital->getAdditionalAmount() + $hospital->getMainAmount());
        $entityManager->persist($hospital);
        $entityManager->flush();
    }
}