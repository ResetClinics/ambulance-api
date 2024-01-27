<?php

namespace App\Query\PartnerReward;

use App\Repository\Partner\Agreement\AgreementRepository;
use Doctrine\DBAL\Connection;

class Fetcher
{
    public function __construct(
        private readonly Connection $connection,
        private readonly AgreementRepository $agreements,
    ) {}
    public function fetch(Query $query): int
    {
        $agreement = $this->agreements->findCurrentByPartnerId(
            $query->partnerId,
            $query->time
        );

        if (!$agreement){
            return 0;
        }

        $qb = $this->connection->createQueryBuilder()
            ->select('r.percent as value')
            ->from('row', 'r')
            ->leftJoin('r', 'agreement', 'a' , 'a.id = r.agreement_id')
            ->andWhere('a.partner_id = :partner')
            ->setParameter('partner', $query->partnerId)
            ->andWhere('a.id = :agreementId')
            ->setParameter('agreementId', $agreement->getId())
            ->andWhere('r.service_id = :service')
            ->setParameter('service', $query->serviceId)
            ->andWhere('r.repeat_number <= :repeat')
            ->setParameter('repeat', $query->repeat)
            ->andWhere('r.distance <= :distance')
            ->setParameter('distance', $query->distance)
        ;

        $qb->orderBy('r.repeat_number', 'DESC');
        $stmt = $qb->executeQuery();

        $row = $stmt->fetchAllAssociative() ?: [];

        $data = array_shift($row);

        if (!$data){
            return 0;
        }

        $result = array_shift($data);

        return $result !== null ? (int)$result : 0;
    }
}