<?php

namespace App\Repository;

use App\Entity\Calling;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}
