<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\GenreLitteraireRepository;
use App\Repository\LongueurPartieRepository;
use App\Repository\PartieRepository;
use App\Repository\PartieEtatRepository;
use App\Repository\PartieJoueurRepository;
use App\Repository\SousCategorieEtapeRepository;

use App\Entity\Partie;
use App\Form\CreerPartieType;
use App\Entity\PartieJoueur;

use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/partie-multi', name: 'app_partie-multi_')]
#[IsGranted(
    'ROLE_USER', 
    statusCode: 403, 
    message: 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.')
]
class PartieMultijoueur extends AbstractController
{
    #[Route('/creer', name: 'creer')]
    public function index(Request $request, 
                          EntityManagerInterface $em, 
                          PartieRepository $ReposPartie, 
                          GenreLitteraireRepository $ReposGenreLitteraire, 
                          LongueurPartieRepository $ReposLongueurPartieRepository, 
                          SousCategorieEtapeRepository $ReposSousCategorie): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response('Aucun utilisateur connecté.');
        }

        //0) Si le joueur a déjà une partie en cours => redirige automatiquement vers cette partie
        $partieDejaExistante= $ReposPartie->JoueurADejaUnePartieDeCeType($user->getId(), $this->getParameter("CONST_ETAT_PARTIE__EN_COURS"));
        if (!empty($partieDejaExistante))
        {
            $this->addFlash('info', 'Vous êtes déjà inscrit à une partie en cours. Vous ne pouvez pas jouer à 2 parties en même temps!');
            return $this->redirectToRoute('app_jouer_multi__demarrer', ['codeConnexion'=>$partieDejaExistante[0]->getCodeConnexion()]); //app_lancer_partie_multi
        }
        
        //1) Si le joueur a déjà une partie en cours de connexion => redirige automatiquement vers cette partie
        $partieDejaExistante= $ReposPartie->JoueurADejaUnePartieEnCoursDeConnexion($user->getId());
               
        if (!empty($partieDejaExistante))
        {
            $this->addFlash('info', 'Vous êtes déjà inscrit à une partie. Vous ne pouvez pas en créer une autre!');
            return $this->redirectToRoute('app_partie-multi_lancer', ['codeConnexion'=>$partieDejaExistante[0]->getCodeConnexion()]); //app_lancer_partie_multi
        }

        //Si le joueur n'a créé aucune partie qui soit encore dans l'état en cours de connexion => On le laisse en créer une
        else 
        {
            $listeGenreLitteraire = $ReposGenreLitteraire->getGenreLitteraireActifByNom();   
            $genreLitteraireParDefaut = $ReposGenreLitteraire->getGenreLitteraireParDefautByNom( $this->getParameter('CONST_GENRE_LITT_PAR_DEFAUT'));
            $longueurPartieParDefaut = $ReposLongueurPartieRepository->findLongueurPartieByNom($this->getParameter('CONST_NOM_LONGUEUR_PARTIE_PAR_DEFAUT'));
            $nbreEpreuveMax = $ReposSousCategorie->retournerNbreSousCategorie();
            
            $partie = new Partie();
            $partie->setGenreLitteraire($genreLitteraireParDefaut);

            $form = $this->createForm(CreerPartieType::class, $partie, [
                'genreLitteraireParDefaut' => $genreLitteraireParDefaut, //On lui passe le genre littéraire par défaut afin de présélectionner cette valeur
                'longueurPartieParDefaut'  => $longueurPartieParDefaut,  //Idem avec le nbre d'épreuve par défaut
                'nbreEpreuveMax' => $nbreEpreuveMax
            ]);
        
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()) {
                $partie = $form->getData();

                $em->persist($partie);
                $em->flush();

                //Création d'un code temporaire vraiment unique (en y injectant l'id de la partie)
                $id = $partie->getId();
                $nbreCarAvSep = 4; //Nbre de caractères avant chaque séparateur
                $code_connexion = substr($partie->getCode_connexion(),0,$nbreCarAvSep).substr($id,-3).substr($partie->getCode_connexion(),$nbreCarAvSep);
                $code_connexion =  implode('-', str_split($code_connexion, $nbreCarAvSep));
                $partie->setCodeConnexion($code_connexion);
              
                $em->persist($partie);
                $em->flush();

                $partieJoueur = new PartieJoueur();
                
                $partieJoueur->setJoueur($user);
                $partieJoueur->setPartie($partie);
                $partieJoueur->setEstCreateur(true);
                
                $em->persist($partieJoueur);
                $em->flush();

                //Redirection sur la page de lancement de la partie
                return $this->redirectToRoute('app_partie-multi_lancer', ['codeConnexion'=> $partie->getCodeConnexion()]);  //$partie->getId() , ['id'=> "tt"]
            }
            
            return $this->render('partie_multi/creer/creer.html.twig', [
                'controller_name' => 'CreerPartieMultijoueur',
                'form'=> $form,
                'listeGenreLitteraire'=> $listeGenreLitteraire,
                'genreLitteraireParDefaut' => $genreLitteraireParDefaut  //On lui passe le genre littéraire par défaut afin de présélectionner cette valeur
            ]);
        }
    }

    //Route utilisée par le créateur (une fois la partie créée), mais également par les joueurs (une fois qu'ils ont rejoint la partie)
    #[Route('/lancer/{codeConnexion}', name: 'lancer')] //, methods:"POST"
    public function lancer_partie(string $codeConnexion='', PartieRepository $ReposPartie, PartieJoueurRepository $ReposPartieJoueur, PartieEtatRepository $ReposPartieEtat, EntityManagerInterface $em): Response 
    {
        $unePartie = $ReposPartie->getPartieByCode($codeConnexion);
       
        if (!empty($unePartie))
        {
            $listeJoueurs = $ReposPartieJoueur->findJoueurByIdpartie($unePartie->getId());

            $user = $this->getUser();
            if (!$user) {
                return new Response('Aucun utilisateur connecté.');
            }
            
            //Si le joueur n'est pas encore connecté à la partie, on le rajoute en tant que simple joueur
            if (!$ReposPartieJoueur->JoueurDejaEnregistreDansPartie($user->getId(), $unePartie->getId()))
            {
                $partieJoueur = new PartieJoueur();
                $partieJoueur->setJoueur($user);
                $partieJoueur->setPartie($unePartie);
                $partieJoueur->setEstCreateur(false);
            
                $em->persist($partieJoueur);
                $em->flush();

                $listeJoueurs = $ReposPartieJoueur->findJoueurByIdpartie($unePartie->getId());
            }
            
            //Rechercher si l'utilisateur est le créateur de la partie. (Seul le créateur doit pouvoir lancer la partie)
            $estCreateur = $ReposPartieJoueur->estCreateur($unePartie->getId(), $user->getId());

            $etatPartieEnCours = $ReposPartieEtat->findEtatPartieByNom($this->getParameter('CONST_ETAT_PARTIE__EN_COURS'));                      //récupère l'état partie en cours 
            $etatPartieTerminee = $ReposPartieEtat->findEtatPartieByNom($this->getParameter('CONST_ETAT_PARTIE__TERMINEE'));                     //récupère l'état partie terminee 
            $etatPartieEnCoursConnexion = $ReposPartieEtat->findEtatPartieByNom($this->getParameter('CONST_ETAT_PARTIE__EN_COURS_DE_CONNEXION'));//récupère l'état partie En cours de connexion
            $etatPartieAbandonnee = $ReposPartieEtat->findEtatPartieByNom($this->getParameter('CONST_ETAT_PARTIE__ABANDONNEE'));                 //récupère l'état partie abondonnée  
            
            return $this->render('partie_multi/lancer.html.twig', [
             'partie' => $unePartie,
             'listeJoueur' => $listeJoueurs,
             'estCreateur' => $estCreateur,
             'etat_partie' => $unePartie->getEtat(),
             'code_partie_en_cours' =>  $etatPartieEnCours->getId(),
             'code_partie_terminee' =>  $etatPartieTerminee->getId(),
             'code_partie_en_cours_de_connexion' =>  $etatPartieEnCoursConnexion->getId(),
             'code_partie_abondonnee' => $etatPartieAbandonnee->getId(),             
            ]);
        }

        //Code de connexion incorrect
        else
        {
            $this->addFlash('info', 'Code de connexion incorrect! Veuillez vérifier le code de connexion et réessayez.');
            return $this->redirectToRoute('app_partie-multi_rejoindre');
        }
    }

    //Page permettant à un joueur de voir le détail d'une partie et de décider s'il veut la rejoindre (le fait de la rejoindre appelle le contrôleur /lancer)
    #[Route('/rejoindre/{codeConnexion}', name: 'rejoindre')] //, methods:"POST"
    public function rejoindre(PartieRepository $ReposPartie, 
                              PartieJoueurRepository $ReposPartieJoueur, 
                              LongueurPartieRepository $ReposLongueurPartie, 
                              GenreLitteraireRepository $ReposGenreLitt, 
                              SousCategorieEtapeRepository $ReposSousCategorie,
                              Request $request,
                              string $codeConnexion = ""
                              ): Response  
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response('Aucun utilisateur connecté.');
        }

        //0) Si le joueur a déjà une partie en cours => redirige automatiquement vers cette partie
        $partieDejaExistante= $ReposPartie->JoueurADejaUnePartieDeCeType($user->getId(), $this->getParameter("CONST_ETAT_PARTIE__EN_COURS"));
        if (!empty($partieDejaExistante))
        {
            $this->addFlash('info', 'Vous êtes déjà inscrit à une partie en cours. Vous ne pouvez pas jouer à 2 parties en même temps!');
            return $this->redirectToRoute('app_jouer_multi__demarrer', ['codeConnexion'=>$partieDejaExistante[0]->getCodeConnexion()]); //app_lancer_partie_multi
        }

        //1) Si le joueur a déjà une partie en cours de connexion => redirige automatiquement vers cette partie
        $partieDejaExistante= $ReposPartie->JoueurADejaUnePartieEnCoursDeConnexion($user->getId());
                
        if (!empty($partieDejaExistante))
        {
            $this->addFlash('info', 'Vous êtes déjà inscrit à une partie. Vous ne pouvez pas participer à 2 parties en même temps!');
            return $this->redirectToRoute('app_partie-multi_lancer', ['codeConnexion'=>$partieDejaExistante[0]->getCodeConnexion()]);
        }
        
        else
        {
            $flagPartie = false;
            $flagPartieValide = false;

            if (!empty($codeConnexion))
            {
                $flagPartie = true;
                $partieARejoindre = $ReposPartie->getPartieByCode($codeConnexion);
                if (!empty($partieARejoindre))
                {
                    //Récupération du créateur de la partie
                    $createur = $ReposPartieJoueur->findCreateurByIdpartie($partieARejoindre->getId());
                    $createur = $createur->getJoueur(); //Renvoie l'ensemble des attributs du joueur
                    $createur = $createur->getlogin();  //Sélectionne uniquement le login

                    //Récupération de la durée de la partie
                    $longueurPartie = $ReposLongueurPartie->find($partieARejoindre->getLongueurPartie());
                    $nbreEpreuveDansTableSCategorie = $ReposSousCategorie->retournerNbreSousCategorie();
                                        
                    //Récupération du genre littéraire
                    $genreLitt = $ReposGenreLitt->find($partieARejoindre->getGenreLitteraire());

                    $flagPartieValide = true;
                }
            }

            if ($flagPartie && $flagPartieValide)
            {
                return $this->render('partie_multi/rejoindre.html.twig', [
                    'partieARejoindre' => $partieARejoindre,
                    'createur' => $createur,
                    'longueurPartie' => $longueurPartie,
                    'genreLitt' => $genreLitt,
                    'nbreEpreuveDansTableSCategorie' => $nbreEpreuveDansTableSCategorie

                ]);
            }
            
            //S'il n'y a pas de code de connexion, ou qu'il est invalide
            else
            {
                if ($flagPartie && !$flagPartieValide )
                {
                    $this->addFlash('info', 'Code de connexion incorrect! Veuillez vérifier le code de connexion et réessayez.');
                }
                return $this->render('partie_multi/rejoindre.html.twig', []);
            }
        }    
    }

    //Le créateur annule la partie
    #[Route('/annuler/{codeConnexion}', name: 'annuler')] //, methods:"POST"
    public function annuler(PartieRepository $ReposPartie, 
                            PartieEtatRepository $ReposPartieEtat, 
                            PartieJoueurRepository $ReposPartieJoueur, 
                            EntityManagerInterface $em, 
                            string $codeConnexion = ""): Response  
    {
        
        if (!empty($codeConnexion))
        {
            $partieARejoindre = $ReposPartie->getPartieByCode($codeConnexion);
            
            if (!empty($partieARejoindre))
            {
                // Vérification que c'est bien le créateur qui a demandé l'annulation
                $createur = $ReposPartieJoueur->findCreateurByIdpartie($partieARejoindre->getId());
                $createur= $createur->getJoueur(); //renvoie l'ensemble des attributs du joueur

                $user = $this->getUser();
                
                if ($user->getId() == $createur->getId())
                {
                    //On annule la partie    
                    $etatPartieEnCours = $ReposPartieEtat->findEtatPartieByNom($this->getParameter('CONST_ETAT_PARTIE__EN_COURS'));                      //récupère l'état partie en cours 
                    $etatPartieTerminee = $ReposPartieEtat->findEtatPartieByNom($this->getParameter('CONST_ETAT_PARTIE__TERMINEE'));                     //récupère l'état partie terminee 
                    $etatPartieEnCoursConnexion = $ReposPartieEtat->findEtatPartieByNom($this->getParameter('CONST_ETAT_PARTIE__EN_COURS_DE_CONNEXION'));//récupère l'état partie En cours de connexion
                    $etatPartieAbandonnee = $ReposPartieEtat->findEtatPartieByNom($this->getParameter('CONST_ETAT_PARTIE__ABANDONNEE'));                 //récupère l'état partie abondonnée  
                         
                    $partieARejoindre->setEtat($etatPartieAbandonnee);   //On annule la partie
                    $em->persist($partieARejoindre);
                    $em->flush();

                    return $this->render('partie_multi/lancer.html.twig', [
                        'partie' => $partieARejoindre,
                        'etat_partie' => $partieARejoindre->getEtat(),
                        'code_partie_en_cours' =>  $etatPartieEnCours->getId(),
                        'code_partie_terminee' =>  $etatPartieTerminee->getId(),
                        'code_partie_en_cours_de_connexion' =>  $etatPartieEnCoursConnexion->getId(),
                        'code_partie_abondonnee' => $etatPartieAbandonnee->getId(),        
                    ]);
                }

                else
                {
                    $this->addFlash('info', 'Annulation impossible. Seul le créateur d\'une partie peut annuler une partie.');
                    return $this->redirectToRoute('app_partie-multi_lancer', ['codeConnexion'=> $partieARejoindre->getCodeConnexion()]);
                }
            }
        }
     
       //Le code de la partie est incorrecte, on retourne à l'accueil
       return $this->redirectToRoute('app_home', []); 
    }    


    //Un joueur (autre que le créateur) annule sa participation à la partie
    #[Route('/annuler-participation/{codeConnexion}', name: 'annuler-participation')] //, methods:"POST"
    public function AnnulerParticipation(PartieRepository $ReposPartie, 
                                         PartieEtatRepository $ReposPartieEtat, 
                                         PartieJoueurRepository $ReposPartieJoueur, 
                                         EntityManagerInterface $em, 
                                         string $codeConnexion = ""): Response  
    {
        if (!empty($codeConnexion))
        {
            $partieAQuitter = $ReposPartie->getPartieByCode($codeConnexion);
            
            if (!empty($partieAQuitter))
            {
                $user = $this->getUser();

                // Vérifie que l'utilisateur est bien connecté à cette partie
                if ($ReposPartieJoueur->JoueurDejaEnregistreDansPartie ($user, $partieAQuitter->getId()))
                {
                    //Vérifie que le joueur n'est pas le créateur
                    if (!$ReposPartieJoueur->estCreateur ($partieAQuitter->getId(), $user->getId()))
                    {
                        //Suppression du joueur dans cette partie
                        $EntitePartieJoueur = $ReposPartieJoueur->findEntityByIdpartieAndIdjoueur($partieAQuitter->getId(), $user);
                        $em->remove( $EntitePartieJoueur);
                        $em->flush();

                        $this->addFlash('info', 'Votre participation à la partie: << '.$codeConnexion.' >> vient d\'être annulée.');
                    }

                    else
                    {
                        $this->addFlash('info', 'Annulation de participation impossible. Le créateur de la partie ne peut pas annuler sa participation à la partie. Mais il peut annuler la partie.');
                        return $this->redirectToRoute('app_partie-multi_lancer', ['codeConnexion'=> $partieAQuitter->getCodeConnexion()]);
                    }
                }

                else{
                    $this->addFlash('info', 'Annulation de participation impossible. Vous n\'êtes pas enregistré dans cette partie!');
                }
            }
        }
       return $this->redirectToRoute('app_home', []); 
    }    
    
    //Envoi au frondend la liste des joueurs
    #[Route('/liste_joueurs/{idPartie}', name: 'liste_joueurs')]
    public function EnvoiListdeJoueurDansUnePartie(PartieJoueurRepository $ReposPartieJoueur, SerializerInterface $serializer, int $idPartie=1): JsonResponse
    {
            //    $data[]= [
            //         [
            //             'login':'thierry',
            //             'estCreateur':true, 
            //         ],
            //         [
            //             'login' => 'louise',
            //             'estCreateur':false,
            //         ],
                    
            //         [
            //             'login' => 'élodie',
            //             'estCreateur':false,
            //         ],
            //     ];
       
            $listeJoueurs = $ReposPartieJoueur->findJoueurByIdpartie($idPartie);
            $jsonListeJoueurs = $serializer->serialize($listeJoueurs, 'json', ['groups' => 'getJoueurs']);

            return new JsonResponse($jsonListeJoueurs, Response::HTTP_OK, [], true);
    }

    //Envoie au frontend l'état d'une partie
    #[Route('/etat/{idPartie}', name: 'etat')]
    public function EnvoiEtatPartie(PartieEtatRepository $ReposPartieEtat, PartieRepository $ReposPartie, SerializerInterface $serializer, int $idPartie=1): JsonResponse
    {
            $partie = $ReposPartie->find($idPartie);
            $etat = $ReposPartieEtat->find($partie->getEtat());
            $jsonEtat = $serializer->serialize($etat, 'json', ['groups' => 'getEtatPartie']);

            return new JsonResponse($jsonEtat, Response::HTTP_OK, [], true);
    }
}