<?php

namespace App\Repository\Hospital;

use App\Entity\Hospital\Hospital;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hospital>
 *
 * @method Hospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hospital[]    findAll()
 * @method Hospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hospital::class);
    }

    public function save(Hospital $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Hospital $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByExternal(string $external): ?Hospital
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.external = :external')
            ->setParameter(':external', $external)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPartnerAndDischargedAt(
        int $partnerId,
        DateTimeImmutable $dischargedAtAfter,
        DateTimeImmutable $dischargedAtBefore
    )
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.partner = :partner')
            ->andWhere('h.dischargedAt >= :dischargedAtAfter')
            ->andWhere('h.dischargedAt < :dischargedAtBefore')
            ->setParameter('partner', $partnerId)
            ->setParameter('dischargedAtAfter', $dischargedAtAfter)
            ->setParameter('dischargedAtBefore', $dischargedAtBefore)
            ->orderBy('h.dischargedAt')
            ->getQuery()
            ->getResult();
    }


    public function findByPartnerAndHospitalizedAt(
        int $partnerId,
        DateTimeImmutable $hospitalizedAtAfter,
        DateTimeImmutable $hospitalizedAtBefore
    )
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.partner = :partner')
            ->andWhere('h.hospitalizedAt >= :hospitalizedAtAfter')
            ->andWhere('h.hospitalizedAt < :hospitalizedAtBefore')
            ->andWhere('h.status = :status')
            ->setParameter('partner', $partnerId)
            ->setParameter('hospitalizedAtAfter', $hospitalizedAtAfter)
            ->setParameter('hospitalizedAtBefore', $hospitalizedAtBefore)
            ->setParameter('status', 'inpatient')
            ->orderBy('h.hospitalizedAt')
            ->getQuery()
            ->getResult();
    }
}
