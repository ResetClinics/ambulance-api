<?php

namespace App\Query\PartnerReward;

use App\Entity\Partner\Agreement\Agreement;
use App\Repository\Partner\Agreement\AgreementRepository;
use Doctrine\DBAL\Connection;
use phpDocumentor\Reflection\Types\This;

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

        $row = $this->getResult($agreement, $query->repeat, $query);

        $data = array_shift($row);

        if (!$data){
            return 0;
        }

        $result = array_shift($data);

        return $result !== null ? (int)$result : 0;
    }

    private function getResult(Agreement $agreement, $repeat, Query $query): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('r.percent as value')
            ->from('row', 'r')
            ->leftJoin('r', 'agreement', 'a' , 'a.id = r.agreement_id')
            ->andWhere('a.id = :agreementId')
            ->setParameter('agreementId', $agreement->getId())
            ->andWhere('r.service_id = :service')
            ->setParameter('service', $query->serviceId)
            ->andWhere('r.repeat_number = :repeat')
            ->setParameter('repeat', $repeat)
            ->andWhere('r.distance <= :distance')
            ->setParameter('distance', $query->distance)
        ;

        $qb->orderBy('r.distance', 'DESC');
        $stmt = $qb->executeQuery();

        $row = $stmt->fetchAllAssociative() ?: [];

        if (count($row) === 0 && $repeat > 0){
            $row = $this->getResult($agreement, $repeat - 1,  $query);
        }

        return $row;
    }
}