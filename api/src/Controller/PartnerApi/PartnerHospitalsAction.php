<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Controller\PaginationSerializer;
use App\Entity\Hospital\Hospital;
use App\Entity\Partner\PartnerUser;
use App\Repository\Hospital\HospitalRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PartnerHospitalsAction extends AbstractController
{
    private const PER_PAGE = 50;

    public function __construct(
        private readonly Security $security,
        private readonly HospitalRepository $hospitals,
        private readonly PaginatorInterface $paginator,
    ) {}

    #[Route('/partner/hospitals', name: 'partner-api.hospitals.index', methods: ['GET'])]
    public function hospitals(Request $request): JsonResponse
    {
        /** @var PartnerUser $user */
        $user = $this->security->getUser();

        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', self::PER_PAGE);

        /** @var string $sort */
        $sort = $request->query->get('sort', 'createdAt');
        /** @var string $direction */
        $direction = $request->query->get('direction', 'desc');
        $search = $request->query->get('search');
        $statuses = $request->query->get('status');

        $dischargedAt = $request->query->get('dischargedAt');
        $dischargedAtAfter = $dischargedAt['after'] ?? null;
        $dischargedAtBefore = $dischargedAt['before'] ?? null;

        $hospitals = $this->hospitals->findAllForPartnerApi(
            $user->getPartner(),
            $sort,
            $direction,
            $search,
            $statuses,
            $dischargedAtAfter,
            $dischargedAtBefore
        );

        $pagination = $this->paginator->paginate($hospitals, $page, $perPage);

        return $this->json(
            [
                'items' => array_map(static function (Hospital $hospital) {
                    return [
                        'id' => $hospital->getId(),
                        'number' => $hospital->getExternal(),
                        'amount' => $hospital->getMainAmount(),
                        'fio' => $hospital->getFio(),
                        'status' => $hospital->getStatus(),
                        'hospitalizedAt' => $hospital->getHospitalized()?->format('d.m.Y H:i'),
                        'dischargedAt' => $hospital->getDischarged()?->format('d.m.Y H:i'),
                    ];
                }, $pagination->getItems()),
                'pagination' => PaginationSerializer::toArray($pagination),
            ]
        );
    }
}
