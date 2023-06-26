<?php

namespace App\Controller\App;

use App\Entity\Calling\Calling;
use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/app', name: 'app_dashboard_index')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(CallingCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->renderContentMaximized()
            ->setDefaultSort([
                'id' => 'DESC',
            ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setLocales([
                'ru' => 'Русский',
            ])
            ->setTitle('Выездная служба');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Вызовы', 'fas fa-home', Calling::class);
        yield MenuItem::section('Администрирование');
        yield MenuItem::linkToCrud('Пользователи', 'fa fa-user', User::class);
    }
}
