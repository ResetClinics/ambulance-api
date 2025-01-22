<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\ShiftPayroll;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShiftPayroll>
 */
class ShiftPayrollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShiftPayroll::class);
    }

    public function removeByShiftId(?int $shiftId): void
    {
        $this->createQueryBuilder('sh')
            ->delete()
            ->where('sh.shift = :shift')
            ->setParameter('shift', $shiftId)
            ->getQuery()
            ->execute();
    }

    public function add(ShiftPayroll $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function findByPlannedEmployee(DateTimeImmutable $accruedAfter, DateTimeImmutable $accruedBefore, int $employeeId)
    {
        return $this->createQueryBuilder('sh')
            ->andWhere('sh.accruedAt >= :accruedAtAfter')
            ->andWhere('sh.accruedAt < :accruedAtBefore')
            ->andWhere('(sh.employee = :employee)')
            ->setParameter('accruedAtAfter', $accruedAfter)
            ->setParameter('accruedAtBefore', $accruedBefore)
            ->setParameter('employee', $employeeId)
            ->orderBy('sh.accruedAt')
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
