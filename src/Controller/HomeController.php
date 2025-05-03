<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        //Liste toutes les images
        $images = glob('assets/images/Caroussel/' . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        $imagePourRegleDuJeu = 'images/RegleDuJeu/VilleFuturiste.jpeg'; /*glob('assets/images/regle/' . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);*/
        

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'imagesCarroussel' => $images,
            'imagePourRegleDuJeu' => $imagePourRegleDuJeu
        ]);
    }
}
