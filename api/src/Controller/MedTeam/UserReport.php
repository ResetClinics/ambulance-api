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
        $report = $this->reports->findOneByMedTeam($medTeam);

        if ($type === 'admin') {
            $message = $this->reportMessageBuilder->build($medTeam, $report, $medTeam->getAdminPrice());
            $this->tgSender->send($medTeam->getAdmin(), $message);
            return $this->json(null);
        }elseif ($type === 'doctor') {
            try {
                $message = $this->reportMessageBuilder->build($medTeam, $report, $medTeam->getDoctorPrice());
                $message = "В целом, конечно, постоянный количественный рост и сфера нашей активности напрямую зависит от экспериментов, поражающих по своей масштабности и грандиозности. И нет сомнений, что активно развивающиеся страны третьего мира указаны как претенденты на роль ключевых факторов. Повседневная практика показывает, что современная методология разработки предполагает независимые способы реализации дальнейших направлений развития. А ещё некоторые особенности внутренней политики рассмотрены исключительно в разрезе маркетинговых и финансовых предпосылок! Задача организации, в особенности же экономическая повестка сегодняшнего дня обеспечивает актуальность своевременного выполнения сверхзадачи. Как принято считать, стремящиеся вытеснить традиционное производство, нанотехнологии подвергнуты целой серии независимых исследований. Наше дело не так однозначно, как может показаться: убеждённость некоторых оппонентов говорит о возможностях укрепления моральных ценностей. Современные технологии достигли такого уровня, что выбранный нами инновационный путь прекрасно подходит для реализации стандартных подходов.";
                $this->tgSender->send($medTeam->getDoctor(), $message);
            }catch (Exception $e) {
                dd($e);
            }
            return $this->json(null);
        }

        throw new NotFoundException();
    }
}