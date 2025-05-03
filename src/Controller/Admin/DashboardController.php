<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Locale;
use App\Entity\GenreLitteraire;
use App\Entity\CategorieEtape;
use App\Entity\SousCategorieEtape;
use App\Entity\LongueurPartie;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(GenreLitteraireCrudController::class)->generateUrl(); //page de garde

        return $this->redirect($url);

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Le jeu de l\'écrivain');
            // ->setLocales([
            //     'en', // locale without custom options
            //     Locale::new('pl', 'polski', 'far fa-language') // custom label and icon
            // ]);
            //->setLocales(['fr', 'pl']);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Page d\'accueil', 'fa fa-home', 'app_home');
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-gauge');
        yield MenuItem::linkToCrud('Genres littéraires', 'fa-solid fa-book', GenreLitteraire::class);
        yield MenuItem::linkToCrud('Catégories d\'épreuves', 'fa-solid fa-list', CategorieEtape::class);
        yield MenuItem::linkToCrud('Sous catégories d\'épreuves', 'fa-solid fa-layer-group', SousCategorieEtape::class);
        yield MenuItem::linkToCrud('Durée des parties', 'fa-solid fa-school', LongueurPartie::class);
    }
}