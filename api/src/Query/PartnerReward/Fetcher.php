<?php

namespace App\Query\PartnerReward;

use Doctrine\DBAL\Connection;

class Fetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {}
    public function fetch(Query $query): int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('r.percent as value')
            ->from('row', 'r')
            ->leftJoin('r', 'agreement', 'a' , 'a.id = r.agreement_id')
            ->andWhere('a.partner_id = :partner')
            ->setParameter('partner', $query->partnerId)
            ->andWhere('r.service_id = :service')
            ->setParameter('service', $query->serviceId)
            ->andWhere('r.repeat_number <= :repeat')
            ->setParameter('repeat', $query->repeat)
            ->andWhere('r.distance <= :distance')
            ->setParameter('distance', $query->distance)
        ;

        $qb->orderBy('r.repeat_number', 'DESC');
        $stmt = $qb->executeQuery();

        $row = $stmt->fetchAssociative() ?: [];

        dump($row);
        $data = array_shift($row);
        dump($data);

        return $data ? (int)$data['value'] : 0;
    }
}