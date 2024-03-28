<?php

namespace App\Repository;

use App\Entity\WorkSchedule;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkSchedule>
 *
 * @method WorkSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkSchedule[]    findAll()
 * @method WorkSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkSchedule::class);
    }

    public function save(WorkSchedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WorkSchedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
       }
    }

    public function findAllByRole(string $role, int $year, int $month)
    {
        $date = new \DateTime();
        $date->setDate($year, $month, 1);
        $date->setTime(0,0);

        $startDate = $date->format('Y-m-d H:i:s');

        $date->add(new \DateInterval('P1M'));
        $endDate = $date->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('u')
            ->leftJoin('u.employee', 'e')
            ->where('u.role = :role')
            ->andWhere('u.workDate >= :start_date')
            ->andWhere('u.workDate < :end_date')
            ->setParameter('role', $role)
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->orderBy('e.name')
            ->getQuery()
            ->getResult();
    }

    public function findAllByUserAndDates(?int $user, ?DateTimeImmutable $dateStart, ?DateTimeImmutable $dateEnd)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.employee', 'e')
            ->andWhere('u.employee = :user')
            ->andWhere('u.workDate >= :start_date')
            ->andWhere('u.workDate < :end_date')
            ->setParameter('user', $user)
            ->setParameter('start_date', $dateStart)
            ->setParameter('end_date', $dateEnd)
            ->getQuery()
            ->getResult();
    }

}
