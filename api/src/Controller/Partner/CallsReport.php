<?php

namespace App\Controller\Partner;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Repository\CallingRepository;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CallsReport extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/api/calls/report', name: 'partner_calls_report', methods: 'GET', priority: 10)]
    public function __invoke(Request $request): JsonResponse
    {

        $completedAtAfter = $request->query->get('completedAtAfter');
        $completedAtBefore = $request->query->get('completedAtBefore');

        $period = new DatePeriod(
            new DateTimeImmutable($completedAtAfter),
            new DateInterval('P1D'),
            new DateTimeImmutable($completedAtBefore));


        $calls = $this->calls->findAllByCompletedAtFromPeriod($period);


        $partners = [];

        $callEntrance = 0;
        $callAccrued = 0;
        $hospitalEntrance = 0;
        $hospitalAccrued = 0;
        $stationaryEntrance = 0;
        $stationaryAccrued = 0;

        /** @var Calling $call */
        foreach ($calls as $call){

            if ($call->getStatus() !== Status::COMPLETED){
                continue;
            }

            if (!key_exists($call->getPartner()->getId(), $partners)){
                $partners[$call->getPartner()->getId()] = [
                    'id' => $call->getPartner()->getId(),
                    'name' => $call->getPartner()->getName(),
                    'calls' => [],
                    'callEntrance' => 0,
                    'callAccrued' => 0,
                    'hospitalEntrance' => 0,
                    'hospitalAccrued' => 0,
                    'stationaryEntrance' => 0,
                    'stationaryAccrued' => 0,
                ];
            }

            $partners[$call->getPartner()->getId()]['calls'][$call->getId()] = [
                'name' => $call->getFio(),
                'callEntrance' => 0,
                'callAccrued' => 0,
                'hospitalEntrance' => 0,
                'hospitalAccrued' => 0,
                'stationaryEntrance' => 0,
                'stationaryAccrued' => 0,
            ];

            foreach ($call->getServices() as $service){
                if ($service->isHospital()){
                    $partners[$call->getPartner()->getId()]['calls'][$call->getId()]['hospitalEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['calls'][$call->getId()]['hospitalAccrued']
                        += $service->getPartnerReward();

                    $partners[$call->getPartner()->getId()]['hospitalEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['hospitalAccrued']
                        += $service->getPartnerReward();

                    $hospitalEntrance  += $service->getPrice();
                    $hospitalAccrued += $service->getPartnerReward();

                }elseif ($service->isStationary()){
                    $partners[$call->getPartner()->getId()]['calls'][$call->getId()]['stationaryEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['calls'][$call->getId()]['stationaryAccrued']
                        += $service->getPartnerReward();

                    $partners[$call->getPartner()->getId()]['stationaryEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['stationaryAccrued']
                        += $service->getPartnerReward();

                    $stationaryEntrance  += $service->getPrice();
                    $stationaryAccrued += $service->getPartnerReward();
                }else{
                    $partners[$call->getPartner()->getId()]['calls'][$call->getId()]['callEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['calls'][$call->getId()]['callAccrued']
                        += $service->getPartnerReward();

                    $partners[$call->getPartner()->getId()]['callEntrance']
                        += $service->getPrice();
                    $partners[$call->getPartner()->getId()]['callAccrued']
                        += $service->getPartnerReward();

                    $callEntrance  += $service->getPrice();
                    $callAccrued += $service->getPartnerReward();
                }
            }

        }

        return $this->json([
            'items' => $partners,
            'callEntrance' => $callEntrance,
            'callAccrued' => $callAccrued,
            'hospitalEntrance' => $hospitalEntrance,
            'hospitalAccrued' => $hospitalAccrued,
            'stationaryEntrance' => $stationaryEntrance,
            'stationaryAccrued' => $stationaryAccrued,
        ]);
    }
}