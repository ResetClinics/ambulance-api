<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\ServicePayroll;
use DateTimeImmutable;
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

    public function findByPlannedEmployee(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore, int $employeeId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.accruedAt >= :accruedAtAfter')
            ->andWhere('c.accruedAt < :accruedAtBefore')
            ->andWhere('(c.employee = :employee)')
            ->setParameter('accruedAtAfter', $accruedAfter)
            ->setParameter('accruedAtBefore', $accruedBefore)
            ->setParameter('employee', $employeeId)
            ->orderBy('c.accruedAt')
            ->getQuery()
            ->getResult();
    }

    public function findByAccruedAt(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.accruedAt >= :accruedAtAfter')
            ->andWhere('s.accruedAt < :accruedAtBefore')
            ->setParameter('accruedAtAfter', $accruedAfter)
            ->setParameter('accruedAtBefore', $accruedBefore)
            ->orderBy('s.accruedAt')
            ->getQuery()
            ->getResult();
    }
}
