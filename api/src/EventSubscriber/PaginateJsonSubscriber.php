<?php

namespace App\EventSubscriber;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Symfony\EventListener\EventPriorities;

use App\Entity\Calling\Calling;
use App\Entity\Hospital\Clinic;
use App\Entity\Hospital\Hospital;
use App\Entity\Partner;
use App\Entity\PaymentSetting\PaymentSetting;
use App\Entity\Service\Category;
use App\Entity\Service\Service;
use App\Entity\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PaginateJsonSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['normalizePaginationResidentialComplex', EventPriorities::PRE_RESPOND],
        ];
    }

    public function normalizePaginationResidentialComplex(
        ViewEvent $event
    ): void
    {

        $method = $event->getRequest()->getMethod();
        if ($method !== Request::METHOD_GET) {
            return;
        }

        /** @var array $class */
        $class = $event->getRequest()->attributes->get('_route_params');

        if (!array_key_exists('_api_resource_class', $class)){
            return;
        }

        /** @var string $apiResourceClass */
        $apiResourceClass = $class['_api_resource_class'];



        if (
            $apiResourceClass !== Partner::class &&
            $apiResourceClass !== Partner\Agreement\Agreement::class &&
            $apiResourceClass !== User::class &&
            $apiResourceClass !== Calling::class &&
            $apiResourceClass !== Category::class &&
            $apiResourceClass !== Service::class &&
            $apiResourceClass !== Hospital::class &&
            $apiResourceClass !== Clinic::class &&
            $apiResourceClass !== PaymentSetting::class
        ) {
            return;
        }

        $data = $event->getRequest()->attributes->get('data');

        if ($data && $data instanceof Paginator) {
            $json = json_decode((string)$event->getControllerResult(), true);
            $pagination = [
                'first' => 1,
                'page' => $data->getCurrentPage(),
                'last' => $data->getLastPage(),
                'pages' => $data->getCurrentPage() - 1 <= 0 ? 1 : $data->getCurrentPage() - 1,
                'next' => $data->getCurrentPage() + 1 > $data->getLastPage() ? $data->getLastPage() : $data->getCurrentPage() + 1,
                'totalItems' => $data->getTotalItems(),
                'itemsPerPage' => $data->getItemsPerPage(),
            ];

            $res = [
                'items' => $json,
                'pagination' => $pagination,
            ];
            $event->setControllerResult(json_encode($res));
        }
    }
}