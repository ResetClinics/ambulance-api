<?php

namespace App\Repository\MedTeam;

use App\Entity\MedTeam\MedTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedTeam>
 *
 * @method MedTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedTeam[]    findAll()
 * @method MedTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedTeam::class);
    }

    public function save(MedTeam $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MedTeam $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLastWorkByNumber(mixed $team): ?MedTeam
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :work')
            ->andWhere('t.phone = :val')
            ->setParameter('val', $team)
            ->setParameter('work', 'work')
            ->orderBy('t.plannedStartAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
