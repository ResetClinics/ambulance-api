<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\Team\Team;
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

}
