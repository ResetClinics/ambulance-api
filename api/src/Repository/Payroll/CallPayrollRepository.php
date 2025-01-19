<?php

declare(strict_types=1);

namespace App\Repository\Payroll;

use App\Entity\Payroll\CallPayroll;
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
}
