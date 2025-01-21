<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\ShiftPayroll;
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
}
