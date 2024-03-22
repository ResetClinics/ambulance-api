<?php

namespace App\Controller\WorkSchedule;

use App\Entity\User\User;
use App\Entity\WorkSchedule;
use App\Repository\UserRepository;
use App\Repository\WorkScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetCollectionMonth extends AbstractController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly WorkScheduleRepository $workSchedules,
    )
    {
    }

    #[Route(path: '/api/work_schedules/month/{role}/{year}/{month}', name: 'work_schedule_month', methods: 'GET')]
    public function __invoke(string $role, int $year, int $month): JsonResponse
    {
        $users = $this->users->findAllByRole($role);
        $workSchedules = $this->workSchedules->findAllByRole($role, $year, $month);

        $numDays = date('t', mktime(0, 0, 0, $month, 1, $year));

        $daysArray = [];
        $totalDays = [];
        $totalTemplate = [
            'daytime' => 0,
            'night' => 0,
            'evening' => 0,
            'day' => 0,
            'stop' => 0,
        ];

        for ($i = 1; $i <= $numDays; $i++) {
            $daysArray[$i] = null;
            $totalDays[$i] = $totalTemplate;
        }

        $result = [
            'total' => $totalDays,
            'users' => []
        ];

        /** @var User $user */
        foreach ($users as $user){
            $result['users'][$user->getId()] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'schedules' => $daysArray,
                'total' =>$totalTemplate
            ];
        }

        /** @var WorkSchedule $workSchedule */
        foreach ($workSchedules as $workSchedule){

            $employeeId = $workSchedule->getEmployee()->getId();

            if (!array_key_exists($employeeId, $result['users'])){
                $result['users'][$employeeId] = [
                    'id' => $employeeId,
                    'name' => $workSchedule->getEmployee()->getName(),
                    'schedules' => $daysArray,
                    'total' =>$totalTemplate
                ];
            }

            $result['users'][$employeeId]['schedules'][$workSchedule->getDay()] = $workSchedule->getType();
            $result['users'][$employeeId]['total'][$workSchedule->getType()] += 1;

            $result['total'][$workSchedule->getDay()][$workSchedule->getType()] += 1;
        }

        return $this->json($result);
    }
}