<?php

namespace App\Query\User\UsersReport;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Repository\CallingRepository;
use App\Services\PeriodService\PeriodService;
use DatePeriod;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class Fetcher
{


    public function __construct(
        public readonly PeriodService     $periodService,
        public readonly CallingRepository $calls,
        private readonly Connection       $connection,
    )
    {
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function fetch(Query $query)
    {
        $period = $this->periodService->createDatePeriodFromRequest($query->period);
        $calls = $this->calls->findAllByCompletedAtFromPeriod($period);

        $roles = [
            'ROLE_DOCTOR',
            'ROLE_ADMIN',
            //'ROLE_DRIVER',
        ];

        $sort = $query->sort;
        $order = $query->order;

        $users = $this->fetchUsers($roles);

        $result = [];

        foreach ($users as $user) {
            $result[$user['id']] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'calls' => 0,
                'amount' => 0,
                'averageCheck' => 0,
                'repeat' => 0,
                'coding' => 0,
            ];
        }

        /** @var Calling $call */
        foreach ($calls as $call) {
            $adminId = $call->getAdmin()->getId();
            $result = $this->getArr($result, $adminId, $call);

            $doctorId = $call->getDoctor()->getId();
            if ($doctorId !== $adminId) {
                $result = $this->getArr($result, $doctorId, $call);
            }
        }

        usort($result, function ($item1, $item2) use ($sort, $order) {
            if ($order === 'desc') {
                return $item2[$sort] <=> $item1[$sort];
            }
            return $item1[$sort] <=> $item2[$sort];
        });

        return $result;
    }

    /**
     * @throws Exception
     */
    public function fetchUsers(array $roles): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'u.id',
                'u.name',
                'u.roles',
            )
            ->from('user', 'u');

        $orX = $qb->expr()->orX();

        foreach ($roles as $key => $role) {
            $orX->add('JSON_CONTAINS(u.roles, :role' . $key . ') = 1');
            $qb->setParameter('role' . $key, json_encode($role));
        }

        $qb->andWhere('u.hide_in_reports = 0')
            ->andWhere($orX);

        $stmt = $qb->executeQuery();
        return $stmt->fetchAllAssociative() ?: [];
    }

    /**
     * @param array $result
     * @param int|null $adminId
     * @param Calling $call
     * @return array
     */
    public function getArr(array $result, ?int $adminId, Calling $call): array
    {
        $result[$adminId]['calls'] += 1;
        $result[$adminId]['amount'] += $call->getPrice();
        if ($result[$adminId]['calls'] > 0) {
            $result[$adminId]['averageCheck']
                = (int)($result[$adminId]['amount'] / $result[$adminId]['calls']);
        }


        foreach ($call->getServices() as $service){
            if ($service->getService()?->getType() === 'replay'){
                $repeat = $this->calls->findOneByOwnerExternalId($call->getNumberCalling());
                if ($repeat){
                    $result[$adminId]['repeat'] += 1;
                }
            }
            break;
        }

        foreach ($call->getServices() as $service){
            if ($service->getService()?->getCategory()?->getId() === 3){
                $result[$adminId]['coding'] += 1;
            }
            break;
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function fitchCalls(DatePeriod $period): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'c.id',
                'c.admin_id as admin',
                'c.doctor_id as doctor',
                'c.completed_at as completed',
                'c.owner_external_id as owner',
                'c.price as price',
                'c.total_amount as total',
                'c.payment_next_order as prepayment',
            )
            ->from('calling', 'c')
            ->andWhere('c.completed_at >= :start')
            ->andWhere('c.completed_at < :end')
            ->andWhere('c.status = :status')
            ->setParameter('start', $period->getStartDate()->format(DateTimeInterface::ATOM))
            ->setParameter('end', $period->getEndDate()->format(DateTimeInterface::ATOM))
            ->setParameter('status', Status::COMPLETED);

        $stmt = $qb->executeQuery();
        return $stmt->fetchAllAssociative() ?: [];
    }
}