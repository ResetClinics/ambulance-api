<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\CallPayroll;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CallPayroll>
 */
class CallPayrollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallPayroll::class);
    }

    public function removeByCallServiceId(?int $callId): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->where('c.call = :call')
            ->setParameter('call', $callId)
            ->getQuery()
            ->execute();
    }

    public function add(CallPayroll $entity): void
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
}
