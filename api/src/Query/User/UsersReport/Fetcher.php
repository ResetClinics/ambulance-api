<?php

namespace App\Query\User\UsersReport;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\Hospital\Hospital;
use App\Entity\MedTeam\MedTeam;
use App\Repository\CallingRepository;
use App\Repository\Hospital\HospitalRepository;
use App\Repository\MedTeam\MedTeamRepository;
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
        public readonly HospitalRepository $hospitals,
        private readonly Connection       $connection,
        private readonly MedTeamRepository $teams,
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
        $hospitals = $this->hospitals->findAllCompletedByHospitalizedAtFromPeriod($period);
        $teams = $this->teams->findByPlanned($period->getStartDate(), $period->getEndDate());

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
                'roles' => json_decode($user['roles'], true),

                //**************

                'revenue' => 0,                     //Выручка ВСЕГО
                'salary' => 'n/a',                  //ЗП ВСЕГО

                'workShiftCount' => 0,              //Смены ВСЕГО шт
                'workShiftHours' => 0,              //Смены ВСЕГО часы
                'workShiftSalary' => 'n/a',        //ЗП Смены ВСЕГО

                'dutyCount' => 0,                   //Дежурства в смену на мероприятиях
                'dutyHours' => 0,                   //Дежурства в смену на мероприятиях
                'dutySalary' => 'n/a',              //ЗП Дежурства в смену на мероприятиях

                'callsRevenue' => 0,                   //Выручка Выезды ВСЕГО
                'callsCount' => 0,                     //
                'callsAverageCheck' => 0,              //
                'callsSalary' => 'n/a',                //

                'callsPrimaryRevenue' => 0,             //Выручка выезды перпичные
                'callsPrimaryCount' => 0,               //
                'callsPrimaryAverageCheck' => 0,        //
                'callsPrimarySalary' => 'n/a',          //

                'callsRepeatRevenue' => 0,          //Выручка выезды повторы
                'callsRepeatCount' => 0,            //
                'callsRepeatAverageCheck' => 0,     //
                'callsRepeatSalary' => 'n/a',       //

                'codingRevenue' => 0,               //Кодирование выручка
                'codingCount' => 0,                 //Кодирование количество
                'codingSalary' => 'n/a',            //Кодирование зарплата

                'hospitalCount' => 0,            //Количество госпитализаций
                'stationaryCount' => 0,            //Количество стационаров
            ];
        }

        /** @var Calling $call */
        foreach ($calls as $call) {
            $adminId = $call?->getAdmin()?->getId();
            $result = $this->getArr($result, $adminId, $call);

            $doctorId = $call?->getDoctor()?->getId();
            if ($doctorId !== $adminId) {
                $result = $this->getArr($result, $doctorId, $call);
            }
        }

        /** @var MedTeam $team */
        foreach ($teams as $team){
            $hours = $team->getPlannedHours();
            $dutyHours = $team->getDutyHours();
            $adminId = $team->getAdmin()->getId();
            if (array_key_exists($adminId,$result)) {
                $result[$adminId]['workShiftCount'] += 1;
                $result[$adminId]['workShiftHours'] += $hours;
                if ($dutyHours > 0){
                    $result[$adminId]['dutyCount'] += 1;
                    $result[$adminId]['dutyHours'] += $dutyHours;
                }
            }
            $doctorId = $team->getDoctor()->getId();
            if ($doctorId !== $adminId && array_key_exists($doctorId,$result)) {
                $result[$doctorId]['workShiftCount'] += 1;
                $result[$doctorId]['workShiftHours'] += $hours;
                if ($dutyHours > 0){
                    $result[$doctorId]['dutyCount'] += 1;
                    $result[$doctorId]['dutyHours'] += $dutyHours;
                }
            }
        }

        /** @var Hospital $hospital */
        foreach ($hospitals as $hospital){
            if ($hospital->getOwner()){

                $call = $hospital->getOwner();
                $adminId = $call?->getAdmin()?->getId();

                if (array_key_exists($adminId, $result)){
                    $result[$adminId]['stationaryCount'] += 1;
                }

                $doctorId = $call?->getDoctor()?->getId();
                if ($doctorId !== $adminId) {
                    if (array_key_exists($doctorId, $result)){
                        $result[$adminId]['stationaryCount'] += 1;
                    }
                }
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
            ->from('user', 'u')
            ->andWhere('u.active = 1')
        ;

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
        if (!array_key_exists($adminId, $result)){
            return  $result;
        }

        $result[$adminId]['revenue'] += $call->getPrice();

        // вызовы всего
        $result[$adminId]['callsCount'] += 1;
        $result[$adminId]['callsRevenue'] += $call->getPrice();
        if ($result[$adminId]['callsCount'] > 0) {
            $result[$adminId]['callsAverageCheck']
                = (int)($result[$adminId]['callsRevenue'] / $result[$adminId]['callsCount']);
        }

        //первычные вызовы
        if ($call->getCountRepeat() === 0){
            $result[$adminId]['callsPrimaryCount'] += 1;
            $result[$adminId]['callsPrimaryRevenue'] += $call->getPrice();
            if ($result[$adminId]['callsPrimaryCount'] > 0) {
                $result[$adminId]['callsPrimaryAverageCheck']
                    = (int)($result[$adminId]['callsPrimaryRevenue'] / $result[$adminId]['callsPrimaryCount']);
            }
        }else{ // вторичные вызовы
            $result[$adminId]['callsRepeatCount'] += 1;
            $result[$adminId]['callsRepeatRevenue'] += $call->getPrice();
            if ($result[$adminId]['callsRepeatCount'] > 0) {
                $result[$adminId]['callsRepeatAverageCheck']
                    = (int)($result[$adminId]['callsRepeatRevenue'] / $result[$adminId]['callsRepeatCount']);
            }
        }

        foreach ($call->getServices() as $service){
            if ($service->getService()?->getCategory()?->getId() === 3){
                $result[$adminId]['codingCount'] += 1;
                $result[$adminId]['codingRevenue'] += $service->getPrice();
            }elseif ($service->getService()->getType() === 'hospital'){
                $result[$adminId]['hospitalCount'] += 1;
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