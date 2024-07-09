<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\Team\Team;
use App\Entity\User\User;
use DatePeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Calling>
 *
 * @method Calling|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calling|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calling[]    findAll()
 * @method Calling[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calling::class);
    }

    public function add(Calling $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function save(Calling $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Calling $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActiveByAdministrator(User $user): ?Calling
    {
        $statuses = [
            Status::ACCEPTED,
            Status::ARRIVED,
        ];

        $qb = $this->createQueryBuilder('c');

        $teams = $qb
            ->andWhere('c.admin = :admin')
            ->setParameter(':admin', $user->getId())
            ->andWhere($qb->expr()->in('c.status', $statuses))
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return array_shift($teams);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCurrentByTeam(Team $team): Calling
    {
        $statuses = [
            Status::ACCEPTED,
            Status::ASSIGNED,
            Status::ARRIVED,
        ];

        $qb = $this->createQueryBuilder('c');

        if (!$checkWordstat = $qb
            ->andWhere('c.team = :team')
            ->setParameter(':team', $team->getId())
            ->andWhere($qb->expr()->in('c.status', $statuses))
            ->getQuery()
            ->getOneOrNullResult()
        ) {
            throw new NotFoundHttpException('Нет текущего заказа');
        }
        return $checkWordstat;
    }


    public function findOneByNumber(string $numberCalling): ?Calling
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.numberCalling = :numberCalling')
            ->setParameter(':numberCalling', $numberCalling)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByOwnerExternalId(string $ownerExternalId): ?Calling
    {
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.ownerExternalId = :ownerExternalId')
            ->setParameter(':ownerExternalId', $ownerExternalId)
            ->getQuery()
            ->getResult();

        return array_shift($result);
    }

    public function findAllByCompletedAtFromPeriod(DatePeriod $period): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere('c.completedAt >= :start')
            ->andWhere('c.completedAt < :end')
            ->setParameter('start', $period->getStartDate())
            ->setParameter('end', $period->getEndDate())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByClientNull(): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere('c.client IS NULL')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllWhoHasOperator()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.operator IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllCoords(): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->select('c.lat, c.lon')
            ->andWhere('c.lat IS NOT NULL')
            ->andWhere('c.lon IS NOT NULL')
            ->andWhere('c.status = :status')
            ->setParameter('status', Status::COMPLETED)
            ->getQuery()
            ->getResult()
            ;
    }
}
