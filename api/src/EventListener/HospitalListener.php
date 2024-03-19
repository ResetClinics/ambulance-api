<?php

namespace App\EventListener;

use App\Entity\Hospital\Hospital;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class HospitalListener
{
    public function postUpdate(Hospital $hospital, LifecycleEventArgs $args)
    {
        $entityManager = $args->getObjectManager();

        // Обновляем значение amount при каждом обновлении main или additional
        $hospital->setAmount($hospital->getMainAmount() + $hospital->getAdditionalAmount());
        $entityManager->persist($hospital);
        $entityManager->flush();
    }
}