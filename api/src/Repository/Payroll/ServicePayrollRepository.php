<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\ServicePayroll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServicePayroll>
 */
class ServicePayrollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServicePayroll::class);
    }

    public function removeByCallServiceId(?int $callServiceIdd): void
    {
        $this->createQueryBuilder('s')
            ->delete()
            ->where('s.callService = :callService')
            ->setParameter('callService', $callServiceIdd)
            ->getQuery()
            ->execute();
    }

    public function add(ServicePayroll $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
