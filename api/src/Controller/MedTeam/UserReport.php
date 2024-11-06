<?php

namespace App\Controller\MedTeam;
use App\Entity\MedTeam\MedTeam;
use App\Entity\User\User;
use App\Repository\AdministratorReportRepository;
use App\Repository\MedTeam\MedTeamRepository;
use App\Repository\UserRepository;
use App\Services\MedTeam\MedTeamReportMessageBuilder;
use App\Services\TelegramSender;
use DateTimeImmutable;
use Exception;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserReport extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository $medTeams,
        private readonly AdministratorReportRepository $reports,
        private readonly UserRepository $users,
        private readonly MedTeamReportMessageBuilder $reportMessageBuilder,
        private TelegramSender                $tgSender,
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/api/med_teams/{id}/report/{type}', name: 'med_teams_user_report', methods: 'GET', priority: 10)]
    public function __invoke(MedTeam $medTeam, string $type, Request $request): JsonResponse
    {
        dd($medTeam->getAdmin()->getName());
        $report = $this->reports->findOneByMedTeam($medTeam);

        if ($type === 'admin') {
            $message = $this->reportMessageBuilder->build($medTeam, $report, $medTeam->getAdminPrice());
            dd($message);
            $this->tgSender->send($medTeam->getAdmin(), $message);
            return $this->json(null);
        }elseif ($type === 'doctor') {
            $message = $this->reportMessageBuilder->build($medTeam, $report, $medTeam->getDoctorPrice());
            dd($message);
            $this->tgSender->send($medTeam->getDoctor(), $message);
            return $this->json(null);
        }

        throw new NotFoundException();
    }
}