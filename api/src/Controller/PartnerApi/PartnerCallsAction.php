<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Controller\PaginationSerializer;
use App\Entity\Calling\Calling;
use App\Entity\Partner\PartnerUser;
use App\Repository\CallingRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PartnerCallsAction extends AbstractController
{
    private const PER_PAGE = 50;

    public function __construct(
        private readonly Security $security,
        private readonly CallingRepository $calls,
        private readonly PaginatorInterface $paginator,
    ) {}

    #[Route('/partner/calls', name: 'partner-api.calls.index', methods: ['GET'])]
    public function calls(Request $request): JsonResponse
    {
        /** @var PartnerUser $user */
        $user = $this->security->getUser();

        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', self::PER_PAGE);

        /** @var string $sort */
        $sort = $request->query->get('sort', 'dateTime');
        /** @var string $direction */
        $direction = $request->query->get('direction', 'desc');

        $search = $request->query->get('search');

        $statuses = $request->query->get('status');

        $completedAt = $request->query->get('completedAt');
        $completedAtAfter = $completedAt['after'] ?? null;
        $completedAtAtBefore = $completedAt['before'] ?? null;

        $calls = $this->calls->findAllForPartnerApi(
            $user->getPartner(),
            $sort,
            $direction,
            $search,
            $statuses,
            $completedAtAfter,
            $completedAtAtBefore
        );

        $pagination = $this->paginator->paginate($calls, $page, $perPage);

        return $this->json(
            [
                'items' => array_map(static function (Calling $call) {
                    return [
                        'id' => $call->getId(),
                        'fio' => $call->getFio(),
                        'number' => $call->getNumberCalling(),
                        'address' => $call->getAddress(),
                        'price' => $call->getPrice(),
                        'reward' => $call->getPartnerReward(),
                        'status' => $call->getStatus(),
                        'dateTime' => $call->getDateTime()?->format('d.m.Y H:i'),
                        'createdAt' => $call->getCreatedAt()?->format('d.m.Y H:i'),
                        'completedAt' => $call->getCompletedAt()?->format('d.m.Y H:i'),
                        'location' => $call->getLon() && $call->getLat() ? [$call->getLat(), $call->getLon()] : null, ];
                }, $pagination->getItems()),
                'pagination' => PaginationSerializer::toArray($pagination),
            ]
        );
    }
}
