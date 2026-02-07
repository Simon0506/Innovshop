<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\OptionGroup;
use App\Entity\OptionType;
use App\Entity\OptionValue;
use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_user_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Innovshop');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-list', Categories::class);
        yield MenuItem::linkToCrud('Produits', 'fa fa-box', Products::class);
        yield MenuItem::linkToCrud('Types d\'options', 'fa fa-tags', OptionGroup::class);
        yield MenuItem::linkToCrud('Valeurs d\'options', 'fa fa-tag', OptionValue::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-shopping-cart', Orders::class);
        yield MenuItem::linkToRoute('Retour site', 'fa fa-home', 'app_home');
    }
}
