<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\Options;
use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_user_index');
        
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Innovshop Symfony');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Clients', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-list', Categories::class);
        yield MenuItem::linkToCrud('Options', 'fa fa-filter', Options::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-cart-flatbed', Orders::class);
        yield MenuItem::linkToCrud('Produits', 'fa fa-tag', Products::class);
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-right-to-bracket', 'app_home');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addWebpackEncoreEntry('admin');
    }
}
