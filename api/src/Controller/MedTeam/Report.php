<?php

namespace App\Controller\MedTeam;
use App\Entity\Hospital\Hospital;
use App\Entity\MedTeam\MedTeam;
use App\Entity\User\User;
use App\Query\PartnerReward\Fetcher;
use App\Query\PartnerReward\Query;
use App\Repository\Hospital\HospitalRepository;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Report extends AbstractController
{
    public function __construct(
        private readonly Fetcher $partnerRewardFetcher,
        private readonly HospitalRepository $hospitals,
        private readonly MedTeamRepository $medTeams,
        private readonly UserRepository $users
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/api/med_teams/report', name: 'med_teams_report', methods: 'GET', priority: 10)]
    public function __invoke(Request $request): JsonResponse
    {

        $startDate = $request->query->get('startDate');
        $endDate= $request->query->get('endDate');

        $startDate  =new DateTimeImmutable($startDate);
        $month = (int)$startDate->format('m');
        $year = (int)$startDate->format('Y');

        $medTeams = $this->medTeams->findByPlanned(
            $startDate,
            new DateTimeImmutable($endDate),
        );

        $users = $this->users->findAllByRole('ROLE_ADMIN');

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
            $result['users'][$user->getName()] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'clocks' => $daysArray,
                'total' =>$totalTemplate
            ];
        }

        /** @var MedTeam $medTeam */
        foreach ($medTeams as $medTeam){

            $employeeName = $medTeam->getAdmin()->getName();
            if (!array_key_exists($employeeName, $result['users'])){
                $result['users'][$employeeName] = [
                    'id' => $medTeam->getAdmin()->getId(),
                    'name' => $employeeName,
                    'clocks' => $daysArray,
                    'total' =>$totalTemplate
                ];
            }

            $result['users'][$employeeName]['clocks'][$medTeam->getDay()] = $medTeam->getPlannedHours();
            $result['users'][$employeeName]['total'][$medTeam->getType()] += 1;

            $result['total'][$medTeam->getDay()][$medTeam->getType()] += 1;
        }

        return $this->json($result);
    }
}