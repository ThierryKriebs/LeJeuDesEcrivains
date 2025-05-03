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
use App\Repository\PartieJoueurRepository;
use App\Repository\PartieEtatRepository;
use App\Repository\SousCategorieEtapeRepository;
use App\Repository\EpreuveEtatRepository;
use App\Repository\PartieEpreuveRepository;
use App\Repository\RedactionRepository;
use App\Repository\NotationRepository;

use App\Entity\Partie;
use App\Entity\PartieEpreuve;
use App\Entity\SousCategorieEtape;
use App\Form\CreerPartieType;
use App\Entity\PartieJoueur;
use App\Entity\Redaction;
use App\Entity\Notation;

use App\Form\RedactionFormType;
use App\Form\NotationFormType;
use App\Form\NotationsFormType;

use DateTimeImmutable;
use DateInterval;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/jouer-multi', name: 'app_jouer_multi__')]
#[IsGranted(
    'ROLE_USER', 
    statusCode: 403, 
    message: 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.')
]
class JouerController extends AbstractController
{

    //Gère le déroulement de la partie (différentes étapes + notation)
    #[Route('/demarrer/{codeConnexion}', name: 'demarrer')]
    public function index(string $codeConnexion, 
                          EntityManagerInterface $em, 
                          PartieRepository $ReposPartie, 
                          PartieJoueurRepository $ReposJoueurs, 
                          PartieEtatRepository $ReposEtat, 
                          SousCategorieEtapeRepository $ReposSousCategorie, 
                          EpreuveEtatRepository $ReposEpreuveEtat) : Response
    {   
       //Récupération de la partie grâce au code de connexion.
       $partie = $ReposPartie->getPartieByCode($codeConnexion);
       if (empty($partie))
       {
            $this->addFlash('info', 'Désolé, les informations sur la partie n\'ont pas pu être récupérées! Assurez-vous que le code de la partie est correct, puis réessayez!');
            return $this->redirectToRoute('app_home', []);
       }

       //Récupération de l'utilisateur
       $user = $this->getUser();
        if (!$user) 
        {
            $this->addFlash('info', 'Désolé, vous devez être connecté pour pouvoir jouer. Opération annulée!');
            return $this->redirectToRoute('app_home', []);
        }

       //Vérif si l'utilisateur est inscrit dans cette partie
       if(!$ReposJoueurs->JoueurDejaEnregistreDansPartie($user->getId(), $partie->getId()))
       {
           $this->addFlash('info', "Désolé, vous n'êtes pas enregistré dans cette partie, vous ne pouvez donc pas y jouer!");
           return $this->redirectToRoute('app_home', []);
       }

       //Si la partie est en cours de connexion => change l'état de la partie en : "EN_COURS"  + création d'une première épreuve
       if ($partie->getEtat()->getNom() === $this->getParameter("CONST_ETAT_PARTIE__EN_COURS_DE_CONNEXION"))
       {
            $createur = $ReposJoueurs->findCreateurByIdpartie($partie->getId());
            
            //Vérification que le joueur est le créateur de la partie:
            if($user->getId() != $createur->getJoueur()->getId())
            {
                $this->addFlash('info', "Désolé, seul le créateur peut lancer la partie. Opération annulée!");
                return $this->redirectToRoute('app_partie-multi_lancer', ['codeConnexion'=> $partie->getCodeConnexion()]);
            }
            
            //Vérification qu'il y a au moins 2 joueurs dans la partie
            $listeJoueurs = $ReposJoueurs->findJoueurByIdpartie($partie);
            if (empty($partie) || count($listeJoueurs) <= 1)
            {
                    $this->addFlash('info', 'Pour pouvoir lancer une partie, vous devez au minimum être 2 joueurs actifs!');
                    return $this->redirectToRoute('app_partie-multi_lancer', ['codeConnexion'=> $partie->getCodeConnexion()]);
            } 

            //Lance la partie => Change son état
            $partie->setEtat($ReposEtat->findEtatPartieByNom($this->getParameter("CONST_ETAT_PARTIE__EN_COURS")));
            
            //Enregistrement d'une première épreuve dans la table partie_etape
            $partieEpreuve = new PartieEpreuve();
            $partieEpreuve->setPartie($partie);
            $partieEpreuve->setNumEtape(1);
            
            //Recherche et injection de l'état ETAT_EPREUVE__VA_DEMARRER
            $EtatEpreuveVaDemarrer = $ReposEpreuveEtat->findOneEpreuveEtat($this->getParameter("CONST_ETAT_EPREUVE__VA_DEMARRER"));
            $partieEpreuve->setEtatEpreuve($EtatEpreuveVaDemarrer);

            //Génération de la date de début de l'épreuve:
            $dateDebutEpreuve = new DateTimeImmutable();
            $dureeCompteARebour = 20;
            $dateDebutEpreuve = $dateDebutEpreuve->add(new DateInterval("PT".$dureeCompteARebour."S")); //L'épreuve démarre dans le futur (laisse aux joueurs le temps de se connecter)
            $partieEpreuve->setDateDebutEpreuve($dateDebutEpreuve);

            //Récupère une sousCatégorie au hasard
            $nbreSousCategorie = $ReposSousCategorie->retournerNbreSousCategorie();
            $sousCatAuHasard = rand (0, $nbreSousCategorie - 1);

            $listeSousCategorie = $ReposSousCategorie->retournerToutesLesSousCategorie();
            $partieEpreuve->setSousCategorie($ReposSousCategorie->retournerSousCategorieEpreuve($listeSousCategorie[$sousCatAuHasard]->getId() ));
            
            //Sauvegarde des informations en base
            $em->persist($partie);
            $em->persist($partieEpreuve);
            $em->flush();

            //Redirection vers la route pour jouer, qui redirigera la première fois en salle d'attente (La salle d'attente devra le rediriger à la fin du compte à rebour!)
            return $this->redirectToRoute('app_jouer_multi__jouer', [
                'codeConnexion'=> $partie->getCodeConnexion(),
                'messageAttente' => "Vous venez de lancer la partie, vous êtes en attente que tous les joueurs vous rejoignent.",
            ]);
       }

       //Si la partie est en cours 
       else if ($partie->getEtat()->getNom() === $this->getParameter("CONST_ETAT_PARTIE__EN_COURS"))
       {
           //On redirige tout le monde dans la salle d'attente (en attendant que tout le monde arrive) 
           return $this->redirectToRoute('app_jouer_multi__jouer', [
            'codeConnexion'=> $partie->getCodeConnexion(),
           ]); 
       }
    }
       
   
    #[Route('/jouer/{codeConnexion}/{messageAttente?}', name: 'jouer')]
    public function jouer(string $codeConnexion, 
                          $messageAttente, 
                          Request $request,
                          EntityManagerInterface $em,
                          PartieRepository $ReposPartie, 
                          PartieJoueurRepository $ReposJoueurs, 
                          PartieEpreuveRepository $ReposEpreuve, 
                          SousCategorieEtapeRepository $ReposSousCategorie,
                          EpreuveEtatRepository $ReposEpreuveEtat,
                          RedactionRepository $ReposRedaction,
                          PartieEtatRepository $ReposEtat,
                          NotationRepository $ReposNotation,
                          ): Response
    {
        if(!isset($messageAttente))
        {
            $messageAttente = 'Vous êtes en attente que tous les joueurs se connectent.';
        }

        //-----------------------
        //Vérifications diverses|
        //-----------------------
        //Récupération de la partie grâce au code de connexion.
        $partie = $ReposPartie->getPartieByCode($codeConnexion);
        if (empty($partie))
        {
            $this->addFlash('info', 'Désolé, les informations sur la partie n\'ont pas pu être récupérées! Assurez-vous que le code de la partie est correct, puis réessayez! Code de connexion: <<'.$codeConnexion.">>" );
            return $this->redirectToRoute('app_home', []);
        }

        //Récupération de l'utilisateur
        $user = $this->getUser();
        if (!$user) 
        {
            $this->addFlash('info', 'Désolé, vous devez être connecté pour pouvoir jouer. Opération annulée!');
            return $this->redirectToRoute('app_home', []);
        }

        //Vérifie si l'utilisateur est inscrit dans cette partie
        if(!$ReposJoueurs->JoueurDejaEnregistreDansPartie($user->getId(), $partie->getId()))
        {
           $this->addFlash('info', "Désolé, vous n'êtes pas enregistré dans cette partie, vous ne pouvez donc pas y jouer!");
           return $this->redirectToRoute('app_home', []);
        }

        //Vérifie si la partie est encore en cours de connexion => redirige
        if ($partie->getEtat()->getNom() === $this->getParameter("CONST_ETAT_PARTIE__EN_COURS_DE_CONNEXION"))
        {
            $this->addFlash('info', "Désolé, le créateur n'a pas encore démarré la partie, vous ne pouvez pas y jouer!");
            return $this->redirectToRoute('app_partie-multi_lancer', ['codeConnexion'=> $partie->getCodeConnexion()]);
        }

        //------------------------------
        //Récupération des informations|
        //------------------------------
        $directory = __DIR__ . '/../../assets/images/Caroussel/*.{jpg,jpeg,png,gif,webp}';
        $imagesCarrousselSalleAttente = glob( $directory, GLOB_BRACE ); //récupération des images sans l'indicatif de versionning de assetmapper

        for ($i=0; $i < count($imagesCarrousselSalleAttente); $i++)
        {
            $imagesCarrousselSalleAttente[$i] = str_replace(__DIR__ . '/../../assets', '', $imagesCarrousselSalleAttente[$i]); //On ne garde que le chemin relatif
        }

        //Récupération des informations de la partie en cours
        $genreLitteraire = $partie->getGenreLitteraire()->getNom();
        $explicationGenreLitteraire = $partie->getGenreLitteraire()->getCommentaire();
        $imageGenreLitteraire = $partie->getGenreLitteraire()->getNomImage();

        //Récupération des informations des épreuves
        $EtatEpreuveNotationVaDEMARRER = $ReposEpreuveEtat->findOneEpreuveEtat($this->getParameter("CONST_ETAT_EPREUVE__NOTATION_VA_DEMARRER"));
        $EtatEpreuveNotationDemarree = $ReposEpreuveEtat->findOneEpreuveEtat($this->getParameter("CONST_ETAT_EPREUVE__NOTATION_DEMARREE"));
        $EtatEpreuveNotationTERMINEE = $ReposEpreuveEtat->findOneEpreuveEtat($this->getParameter("CONST_ETAT_EPREUVE__NOTATION_TERMINEE"));
        
        $nbreEpreuveTotal = $partie->getLongueurPartie()->getNombreEtape();
        $nbreEpreuveTotalNom = $partie->getLongueurPartie()->getNom(); //Courte, longue, très longue...
        $nbreEpreuveDansTableSCategorie = $ReposSousCategorie->retournerNbreSousCategorie();
        if ($nbreEpreuveTotal > $nbreEpreuveDansTableSCategorie)
        {
            $nbreEpreuveTotal = $nbreEpreuveDansTableSCategorie;
        }

        $nbreEpreuveDejaJoue = $ReposEpreuve->CompteNbreEpreuveDejaJoue($partie, $EtatEpreuveNotationTERMINEE->getId());
        $nbreEpreuveDejaEnregistree = $ReposEpreuve->CompteNbreEpreuve($partie); //Nbre d'épreuve que contient la partie qq soit leur état

        //Récupération d'informations sur la partie (utilisée pour actualiser les infos des frontend (epreuve, notation)
        $tabInfos = $this->EnvoiInfosSurPartie(
                                                $ReposEpreuve,                                     
                                                $ReposJoueurs, 
                                                $ReposSousCategorie,
                                                $ReposRedaction,
                                                $ReposNotation,
                                                $partie );

       
        $DUREE_TRANSITION = $this->getParameter("DUREE_TRANSITION"); //Marge entre les différentes partie (Epreuve, notation, partie suivante) => Temps de rafraichissement
        $DUREE_AVANT_RAFRAICHISSEMENT_INFO_SALLE_ATTENTE = $this->getParameter("DUREE_AVANT_RAFRAICHISSEMENT_INFO_SALLE_ATTENTE"); //Temps avant rafraichissement des informations pour les salles d'attentes
       
        //---------------------------------------------------------------------------------------------
        //Si l'épreuve n'a pas encore été lancée, et qu'il est l'heure de la lancer => Lance l'épreuve|
        //---------------------------------------------------------------------------------------------
        if ($tabInfos['epreuveEtat'] === $this->getParameter("CONST_ETAT_EPREUVE__VA_DEMARRER") && $tabInfos['maintenant'] >= $tabInfos['epreuveDateDebut'])
        {
            //On change l'état de l'épreuve à en cours dans la base
            $etatEpreuve_ENCOURS = $ReposEpreuveEtat->findOneEpreuveEtat($this->getParameter("CONST_ETAT_EPREUVE__EN_COURS"));
            $tabInfos['partieEpreuve']->setEtatEpreuve($etatEpreuve_ENCOURS);   
            $tabInfos['epreuveEtat'] = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom();   

            $em->persist($tabInfos['partieEpreuve']);
            $em->flush();
        }
        $epreuveEtat = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom(); //On recharge si précédemment modifié
        
       
        //------------------------------------------------------------------------------------------------------------------
        //Si l'épreuve est en cours ET que (le délais + marge n'est pas terminé || que tous les joueurs n'ont pas terminés)|
        //------------------------------------------------------------------------------------------------------------------
        if ( $epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__EN_COURS") && 
            ($tabInfos['epreuveDateFin']->modify("+".strval($DUREE_TRANSITION)." seconds") > $tabInfos['maintenant'] && $tabInfos['FlagRedacsTerminees'] === 'false') )
        {
            //Recherche si le joueur a déjà écrit une rédaction pour cette épreuve
            $participation = $ReposRedaction->findRedactionForEpreuveByJoueur($user, $tabInfos['partieEpreuve']);

            if ($participation == null)
            {
                $redactions = new Redaction();
                $form = $this->createForm(RedactionFormType::class,  $redactions,[
                   'joueur' => $user->getId(), 
                   'partieEpreuve'  => $tabInfos['partieEpreuve']->getId(),  //Idem avec le nbre d'épreuve par défaut
                ]);
                
                $form->handleRequest($request);
                
                if($form->isSubmitted() && $form->isValid()) 
                {
                    $redactions = $form->getData();
                    
                    $em->persist($redactions);
                    $em->flush();
    
                    //Envoie en salle d'attente
                    return $this->render('partie_multi/jouer/patienter.html.twig', [
                        'codeConnexion'=> $codeConnexion,
                        'messageAttente'=> "Vous êtes en attente que tous les joueurs finissent l'épreuve",
                        'messagedureeAPatienter'=> "La partie reprendra <strong> au plus tard </strong> d'ici: ",
                        'dureeAvantRafraichissement' => $DUREE_AVANT_RAFRAICHISSEMENT_INFO_SALLE_ATTENTE,
                        'dureeRestante' => $tabInfos['dureeRestanteAvantFinEpreuve'],
                                            
                        'partieId' => $partie->getId(),
                        'numeroEpreuve'=> $tabInfos['partieEpreuve']->getNumEtape(),
                        'epreuveEtat' => $tabInfos['epreuveEtat'],
                        'typeAttente' => "salleAttenteFinEpreuve",

                        'imagesCarroussel' =>  $imagesCarrousselSalleAttente,

                        'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
                        'FlagNotationsTerminees' => $tabInfos['FlagNotationsTerminees']
                    ]);
                }

                //Affiche le template de l'épreuve
                return $this->render('partie_multi/jouer/epreuve.html.twig', [
                    'form'=> $form,
                    'genreLitteraire' => $genreLitteraire,
                    'explicationGenreLitteraire' => $explicationGenreLitteraire,
                    'imageGenreLitteraire' => $imageGenreLitteraire,
                    'nbreEpreuveDeLaPartie' => $nbreEpreuveTotal,
                    'nbreEpreuveDansTableSCategorie' => $nbreEpreuveDansTableSCategorie,
                    'nbreEpreuveTotalNom' => $nbreEpreuveTotalNom,
                    'codeConnexion'=> $codeConnexion,
                    'numeroEpreuve'=> $tabInfos['partieEpreuve']->getNumEtape(),
                    'descriptionEpreuve'=> $tabInfos['partieEpreuve']->getSousCategorie()->getExplication(),
                    'dureeRestante' => $tabInfos['dureeRestanteAvantFinEpreuve'],
                    'partieId' => $partie->getId(),
                    'epreuveEtat' => $tabInfos['epreuveEtat'],
                    
                    'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
                    'FlagNotationsTerminees' => $tabInfos['FlagNotationsTerminees']
                ]);
            }

            else
            {
                //Envoie en salle d'attente
                return $this->render('partie_multi/jouer/patienter.html.twig', [
                    'codeConnexion'=> $codeConnexion,
                    'messageAttente'=> "Vous êtes en attente que tous les joueurs finissent l'épreuve.",
                    'messagedureeAPatienter'=> "La partie reprendra <strong> au plus tard </strong> d'ici: ",
                    'dureeAvantRafraichissement' => $DUREE_AVANT_RAFRAICHISSEMENT_INFO_SALLE_ATTENTE,
                    'dureeRestante' => $tabInfos['dureeRestanteAvantFinEpreuve'],

                    'partieId' => $partie->getId(),
                    'numeroEpreuve'=> $tabInfos['partieEpreuve']->getNumEtape(),
                    'epreuveEtat' => $tabInfos['epreuveEtat'],
                    'typeAttente' => "salleAttenteFinEpreuve",

                    'imagesCarroussel' =>  $imagesCarrousselSalleAttente,

                    'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
                    'FlagNotationsTerminees' => $tabInfos['FlagNotationsTerminees']                    
                ]);
            }
        }

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------
        //Sinon si l'épreuve est en cours ET (que le délais est terminé || que tous les joueurs ont terminé leur rédaction) ET que le joueur est le créateur de la partie|
        //----------------------------------------------------------------------------------------------------------------------------------------------------------------
        else if ( $epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__EN_COURS") && 
                  ($tabInfos['maintenant'] >= $tabInfos['epreuveDateFin']->modify("+".strval($DUREE_TRANSITION)." seconds") ||$tabInfos['FlagRedacsTerminees'] === 'true' ) && 
                  $ReposJoueurs->estCreateur ($partie->getId(), $user->getId()))
        {            
            //Vérifie que tous les joueurs ont rendu la rédaction, sinon création des rédactions manquantes
            //1)Récupération de la liste des joueurs
            $listeJoueurs = $ReposJoueurs->findJoueurByIdpartie($partie);

            foreach ($listeJoueurs as $joueur) {
                if  (is_null($ReposRedaction->findRedactionForEpreuveByJoueur($joueur->getJoueur(),  $tabInfos['partieEpreuve']))) {
                    //Création de la rédaction
                    $uneRedaction = new Redaction();
                    $uneRedaction->setPartieEpreuve($tabInfos['partieEpreuve']);
                    $uneRedaction->setJoueur($joueur->getJoueur()  );
                    $uneRedaction->setRedaction("");

                    $em->persist($uneRedaction);
                    $em->flush();
                }
            }

             //Change l'état de l'épreuve à terminée
             $tabInfos['partieEpreuve']->setEtatEpreuve($EtatEpreuveNotationVaDEMARRER);
             $tabInfos['epreuveEtat'] = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom();   
             $tabInfos['partieEpreuve']->setDateFinEpreuve(new DateTimeImmutable());
             $tabInfos['epreuveDateFin'] = $tabInfos['partieEpreuve']->getDateFinEpreuve();
             
             $intervalTemp = $tabInfos['epreuveDateDebutNotation']->diff($tabInfos['maintenant']);
            
             //Rafraichissement des informations
             $tabInfos = $this->EnvoiInfosSurPartie(
                                        $ReposEpreuve,                                     
                                        $ReposJoueurs, 
                                        $ReposSousCategorie,
                                        $ReposRedaction,
                                        $ReposNotation,
                                        $partie );

             //MAJ plus tard: Rajouter une date de fin réelle afin de mieux calculer la durée de la notation (date de fin de la notation)
          
             $em->persist($tabInfos['partieEpreuve']);
             $em->flush();
                    
             $epreuveEtat = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom();
        }

        else if( $epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__NOTATION_VA_DEMARRER") &&
                 $tabInfos['maintenant'] >= $tabInfos['epreuveDateDebutNotation'] )
        {
            //On change l'état de l'épreuve à en cours dans la base
            $tabInfos['partieEpreuve']->setEtatEpreuve($EtatEpreuveNotationDemarree);
            $tabInfos['epreuveEtat'] = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom();   

            $em->persist($tabInfos['partieEpreuve']);
            $em->flush();
        }
        $epreuveEtat = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom(); //On recharge si précédemment modifié

      
        //---------------------------------------------------------------------------------------------------------------------------------------------------------
        //Sinon si l'épreuve est dans l'état NOTATION_DEMARREE ET (que le délais de NOTATION n'est pas terminé || que tous les joueurs n'ont pas terminé de noter)|
        //---------------------------------------------------------------------------------------------------------------------------------------------------------
        if ( $epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__NOTATION_DEMARREE") && 
                 ($tabInfos['maintenant'] < $tabInfos['epreuveDateFinNotation']->modify("+".strval($DUREE_TRANSITION)." seconds") &&  $tabInfos['FlagNotationsTerminees'] === 'false' ))
        {
            //Recherche si le joueur a déjà distribué des notes pour cette épreuve
            $participation = $ReposNotation->findNotationForEpreuveByJoueur($user, $tabInfos['partieEpreuve']);

            if ($participation == null)
            {
                $redactions = $ReposRedaction->getListRedactionMustBeNoteFromAPartieEpreuveFromAPlayer($this->getUser()->getId(), $tabInfos['partieEpreuve']->getId());
                              
                foreach( $redactions as $redac )
                {
                    $notation = new Notation();
                    $notation->setRedaction($redac);
                    $notation->setNoteur($user);

                    $tabNotation[] = $notation;
                }
                
                $form = $this->createForm(NotationsFormType::class,  ['enfants' => $tabNotation],[
                         'noteur'  => $user->getId(),  //Indique le noteur
                        ]);
                
                $form->handleRequest($request);
                
                if($form->isSubmitted() && $form->isValid()) 
                {
                    $tabDonnees = $form->getData();

                    //1 sous-formulaire par rédaction à noter
                    foreach($tabDonnees as $listeSousFormulaires)
                    {
                        foreach($listeSousFormulaires as $sousFormulaireNotation )
                        {
                            $em->persist($sousFormulaireNotation);
                        }
                    }
                    $em->flush();

                    //Envoie en salle d'attente
                    return $this->render('partie_multi/jouer/patienter.html.twig', [
                        'codeConnexion'=> $codeConnexion,
                        'messageAttente'=> "Vous êtes en attente que tous les joueurs finissent de noter.",
                        'messagedureeAPatienter'=> "La partie reprendra <strong> au plus tard </strong> d'ici: ",
                        'dureeAvantRafraichissement' => $DUREE_AVANT_RAFRAICHISSEMENT_INFO_SALLE_ATTENTE,
                        'dureeRestante' => $tabInfos['dureeRestanteAvantFinNotation'],
                    
                        'partieId' => $partie->getId(),
                        'numeroEpreuve'=> $tabInfos['partieEpreuve']->getNumEtape(),
                        'epreuveEtat' => $tabInfos['epreuveEtat'],
                        'typeAttente' => "salleAttenteFinNotation",

                        'imagesCarroussel' =>  $imagesCarrousselSalleAttente,

                        'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
                        'FlagNotationsTerminees' => $tabInfos['FlagNotationsTerminees']
                    ]);
                }
            
                return $this->render('partie_multi/noter/toutNoter.html.twig', [
                    'form'=> $form,
                    'genreLitteraire' => $genreLitteraire,
                    'explicationGenreLitteraire' => $explicationGenreLitteraire,
                    'imageGenreLitteraire' => $imageGenreLitteraire,
                    'nbreEpreuveDeLaPartie' => $nbreEpreuveTotal,
                    'nbreEpreuveDansTableSCategorie' => $nbreEpreuveDansTableSCategorie,
                    'nbreEpreuveTotalNom' => $nbreEpreuveTotalNom,
                    'codeConnexion'=> $codeConnexion,
                    'numeroEpreuve'=> $tabInfos['partieEpreuve']->getNumEtape(),
                    'descriptionEpreuve'=> $tabInfos['partieEpreuve']->getSousCategorie()->getExplication(),
                    'dureeNotation' =>  $tabInfos['dureeRestanteAvantFinNotation'],
                    'partieId' => $partie->getId(),
                    'epreuveEtat' => $tabInfos['epreuveEtat'],

                    'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
                    'FlagNotationsTerminees' => $tabInfos['FlagNotationsTerminees']
                ]);
            }

            else
            {
                 //Envoie en salle d'attente
                 return $this->render('partie_multi/jouer/patienter.html.twig', [
                    'codeConnexion'=> $codeConnexion,
                    'messageAttente'=> "Vous êtes en attente que tous les joueurs finissent de noter.",
                    'messagedureeAPatienter'=> "La partie reprendra <strong> au plus tard </strong> d'ici: ",
                    'dureeAvantRafraichissement' => $DUREE_AVANT_RAFRAICHISSEMENT_INFO_SALLE_ATTENTE,
                    'dureeRestante' => $tabInfos['dureeRestanteAvantFinNotation'],
                    
                    'partieId' => $partie->getId(),
                    'numeroEpreuve'=> $tabInfos['partieEpreuve']->getNumEtape(),
                    'epreuveEtat' => $tabInfos['epreuveEtat'],
                    'typeAttente' => "salleAttenteFinNotation",

                    'imagesCarroussel' =>  $imagesCarrousselSalleAttente,

                    'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
                    'FlagNotationsTerminees' => $tabInfos['FlagNotationsTerminees']
                ]);
            }
        }
        
        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------
        //Sinon si l'épreuve est terminée ET (que le délais de NOTATION est terminé || que tous les joueurs ont terminé de noter) ET que le joueur est le créateur de la partie|
        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------
        else if ( $epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__NOTATION_DEMARREE") && 
                  ($tabInfos['maintenant'] >= $tabInfos['epreuveDateFinNotation']->modify("+".strval($DUREE_TRANSITION)." seconds") ||  $tabInfos['FlagNotationsTerminees'] === 'true') && 
                  $ReposJoueurs->estCreateur ($partie->getId(), $user->getId()) )
        {                      
            //Calcul des scores de l'épreuve + MAJ table Rédaction
            $ReposNotation->calculScoreForAEpreuve($tabInfos['partieEpreuve'], $ReposRedaction, $em);

            //Faire un classement par épreuve (rajouter une colonne) + MAJ table Redaction
            $ReposRedaction->creationClassementForAEpreuve($tabInfos['partieEpreuve'], $em);
            
            //Change l'état de l'épreuve à "NOTATION_TERMINEE"
            $tabInfos['partieEpreuve']->setEtatEpreuve($EtatEpreuveNotationTERMINEE);
            $tabInfos['epreuveEtat'] = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom();   
            $em->persist($tabInfos['partieEpreuve']);
            $em->flush();
            $epreuveEtat = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom();

            $nbreEpreuveDejaJoue = $ReposEpreuve->compteNbreEpreuveDejaJoue($partie, $EtatEpreuveNotationTERMINEE->getId());

            //Si le nombre d'épreuve n'est pas encore atteint    
            if ($nbreEpreuveDejaJoue < $nbreEpreuveTotal )
            {
                //Récupération de l'ancien numéro d'épreuve.
                $numEtapePrec = $tabInfos['partieEpreuve']->getNumEtape();

                //Création d'une nouvelle épreuve liée à la partie en cours
                $partieEpreuve = new PartieEpreuve();
                $partieEpreuve->setPartie($partie);
                                
                //Initialisation du numéro de l'épreuve
                $partieEpreuve->setNumEtape($numEtapePrec + 1);
                                                
                //Initialisation de l'état de l'épreuve à ETAT_EPREUVE__VA_DEMARRER
                $EtatEpreuveVaDemarrer = $ReposEpreuveEtat->findOneEpreuveEtat($this->getParameter("CONST_ETAT_EPREUVE__VA_DEMARRER"));
                $partieEpreuve->setEtatEpreuve($EtatEpreuveVaDemarrer);

                //Génération de la date de de début de l'épreuve:
                $dateDebutEpreuve = new DateTimeImmutable();
                $delaisSuppl = $DUREE_TRANSITION;
                $dateDebutEpreuve = $dateDebutEpreuve->add(new DateInterval("PT".$delaisSuppl."S")); //L'épreuve démarre dans le futur (laisse aux joueurs le temps de se connecter)
                $partieEpreuve->setDateDebutEpreuve($dateDebutEpreuve);
               
                //-------------------------------------------
                //Récupération d'une sousCatégorie au hasard|
                //-------------------------------------------
                $listeSousCategorie = $ReposSousCategorie->retournerIdToutesLesSousCategorie();
                $listeSousCategorieDejaJouee = $ReposEpreuve->ListeEpreuveDejaJoue($partie);
                                
                //On retire de la liste des souscatégorie, les souscategories déjà jouées
                for ($i=0; $i < count($listeSousCategorieDejaJouee); $i++)
                {
                    $cle = array_search($listeSousCategorieDejaJouee[$i], $listeSousCategorie);

                    //Si la sous catégorie a déjà été jouée, on la retire du tableau des sous-catégories
                    if ($cle !== false)
                    {
                        unset($listeSousCategorie[$cle]);
                        $listeSousCategorie = array_values($listeSousCategorie); //réindexation de l'ensemble du tableau!
                    }
                }
           
                $sousCatAuHasard = rand (0, count($listeSousCategorie)  - 1 );
                
                // Initialisation de la sousCategorie trouvée
                $partieEpreuve->setSousCategorie($ReposSousCategorie->retournerSousCategorieEpreuve($listeSousCategorie[$sousCatAuHasard] ));

                //Enregistrement en base
                $em->persist($partieEpreuve);
                $em->flush();
              
                //Rafraichissement des informations
                $tabInfos = $this->EnvoiInfosSurPartie(
                                                          $ReposEpreuve,                                     
                                                          $ReposJoueurs, 
                                                          $ReposSousCategorie,
                                                          $ReposRedaction,
                                                          $ReposNotation,
                                                          $partie );
 
                //Envoie en salle d'attente, (La salle d'attente devra le rediriger à la fin du compte à rebour!)
                return $this->render('partie_multi/jouer/patienter.html.twig', [
                    'codeConnexion'=> $codeConnexion,
                    'messageAttente'=> "Une nouvelle épreuve va être lancée, vous êtes en attente que tous les joueurs vous rejoignent.",
                    'messagedureeAPatienter'=> "La partie reprendra <strong> au plus tard </strong> d'ici: ",
                    'dureeAvantRafraichissement' => $DUREE_AVANT_RAFRAICHISSEMENT_INFO_SALLE_ATTENTE,
                    'dureeRestante' =>$tabInfos["dureeRestanteAvantDebutEpreuve"],
                    
                    'partieId' => $partie->getId(),
                    'numeroEpreuve'=> $tabInfos['partieEpreuve']->getNumEtape(),
                    'epreuveEtat' => $tabInfos['epreuveEtat'],
                    'typeAttente' => "salleAttenteDebutEpreuve",

                    'imagesCarroussel' =>  $imagesCarrousselSalleAttente,

                    'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
                    'FlagNotationsTerminees' => $tabInfos['FlagNotationsTerminees']
                ]);            
            }
        }

        //Si la partie est terminée => Fin
        $nbreEpreuveDejaJoue = $ReposEpreuve->compteNbreEpreuveDejaJoue($partie, $EtatEpreuveNotationTERMINEE->getId());

        if ($nbreEpreuveDejaJoue === $nbreEpreuveTotal )
        {
            //On termine la partie
            $partie->setEtat($ReposEtat->findEtatPartieByNom($this->getParameter("CONST_ETAT_PARTIE__TERMINEE")));
            $em->persist( $partie);
            $em->flush();
                       
            //Calcul du score final de chaque joueur + MAJ table partie_joueur
            $ReposEpreuve->calculScoreFinal($partie,$em);

            //Calcul du classement final + MAJ  table partie_joueur
            $ReposJoueurs->calculClassementFinal($partie,$em);

            //Récupération du classement final
            $listeclassementFinal = $ReposJoueurs->getClassementFinal($partie, $em);
            
            //Récupération du classement par épreuve
            $listeclassementParEpreuve = $ReposRedaction->getclassementByRound($partie);

            //Affichage du classement (et des rédactions)
            return $this->render('partie_multi/jouer/fin.html.twig', [
                'codeConnexion'=> $codeConnexion,
                'messagefin'=> "Merci d'avoir joué, à bientôt!",
                'classementParEpreuvre' => $listeclassementParEpreuve,
                'classementFinal' => $listeclassementFinal
            ]); 
        }
       
        //--------------------------------------------------------------------------
        //Sinon nous sommes en transition affichage du message adequat => Patienter|
        //--------------------------------------------------------------------------
        if ($epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__VA_DEMARRER") && $nbreEpreuveDejaEnregistree === 1) //La première fois qu'on entre dans la controller joue, une épreuve a déjà été enregistrée. 
        {
            $messageAttente = "La partie va débuter sous peu, veuillez patienter."; 
            $messagedureeAPatienter = "La partie débutera dans: ";
            $dureeRestante = $tabInfos['dureeRestanteAvantDebutEpreuve'];
            $typeAttente = "salleAttenteDebutEpreuve";
        }

        else if ($epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__VA_DEMARRER") && $nbreEpreuveDejaEnregistree > 1) //Transition vers une nouvelle épreuve
        {
            $messageAttente = "L'épreuve va démarrer sous peu, veuillez patienter.";
            $messagedureeAPatienter = "L'épreuve débutera <strong> au plus tard </strong> d'ici: ";
            $dureeRestante = $tabInfos['dureeRestanteAvantDebutEpreuve'];
            $typeAttente = "salleAttenteDebutEpreuve";
        }

        else if ($epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__EN_COURS") ||
                 $epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__NOTATION_VA_DEMARRER")) //En attente de la notation
        {
            $messageAttente = "La notation de la dernière épreuve va débuter sous peu, veuillez patienter.";
            $messagedureeAPatienter = "La notation débutera <strong> au plus tard </strong> d'ici: ";
            $dureeRestante =  $tabInfos["dureeRestanteAvantDebutNotation"];
            $typeAttente = "salleAttenteDebutNotation";
        }
      
        else if ($epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__NOTATION_DEMARREE")) //Si délais dépassé et l'utilisateur n'est pas le créateur de la partie
        {
            $messageAttente = "La notation de la dernière épreuve est toujours en cours, veuillez patienter.";
            $messagedureeAPatienter = "La prochaine épreuve devrait débuter <strong> au plus tard </strong> d'ici: ";
            $dureeRestante =  $tabInfos["dureeRestanteAvantFinNotation"];
            $typeAttente = "salleAttenteFinNotation";
        }

        else if ($epreuveEtat === $this->getParameter("CONST_ETAT_EPREUVE__NOTATION_TERMINEE"))
        {
            $messageAttente = "La prochaine épreuve va débuter sous peu, veuillez patienter.";
            $messagedureeAPatienter = "La partie reprendra <strong> au plus tard </strong> d'ici: ";
            $dureeRestante =  $DUREE_TRANSITION;
            $typeAttente = "salleAttenteDebutEpreuve";
        }

        else if ($nbreEpreuveDejaEnregistree >= $nbreEpreuveTotal )
        {
            $messageAttente = "La partie est terminée. Le calcul des résultats est en cours, veuillez patienter.";
            $messagedureeAPatienter = "Les résultats vont apparaître <strong> au plus tard </strong> d'ici: ";
            $dureeRestante =  $DUREE_TRANSITION;
            $typeAttente = "salleAttenteResultatFinal";
        }

        else
        {
            $messageAttente = "ON NE DEVRAIT PLUS TOMBER ICI!";
            $messagedureeAPatienter = "La partie débutera <strong> au plus tard </strong> d'ici: ";
            $dureeRestante = $tabInfos['dureeRestanteAvantDebutEpreuve'];//???!
            $typeAttente = "salleAttenteDebutEpreuve";
        }
              
        //Envoie en salle d'attente        
        //On récupère la date de démarrage de la partie
        return $this->render('partie_multi/jouer/patienter.html.twig', [
            'codeConnexion'=> $codeConnexion,
            'messageAttente'=> $messageAttente,
            'messagedureeAPatienter'=> $messagedureeAPatienter,
            'dureeAvantRafraichissement' => $DUREE_AVANT_RAFRAICHISSEMENT_INFO_SALLE_ATTENTE,
            'dureeRestante' => $dureeRestante,
                    
            'partieId' => $partie->getId(),
            'numeroEpreuve'=> $tabInfos['partieEpreuve']->getNumEtape(),
            'epreuveEtat' => $tabInfos['epreuveEtat'],
            'typeAttente' => $typeAttente,

            'imagesCarroussel' =>  $imagesCarrousselSalleAttente,

            'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
            'FlagNotationsTerminees' => $tabInfos['FlagNotationsTerminees']
        ]);
    }



    //Récupère ou calcul certaines informations de la partie
    //Appelé par le contrôleur Jouer 
    public function EnvoiInfosSurPartie( PartieEpreuveRepository $ReposEpreuve, 
                                         PartieJoueurRepository $ReposJoueurs, 
                                         SousCategorieEtapeRepository $ReposSousCategorie,
                                         RedactionRepository $ReposRedaction,
                                         NotationRepository $ReposNotation,
                                                                                                      
                                         $partie,
                                        ):array
    {
        $DUREE_TRANSITION = $this->getParameter("DUREE_TRANSITION"); //Marge entre les différentes parties (Epreuve, notation, partie suivante) => Temps de rafraichissement
        
        $tabInfos = [
            'partieEpreuve' => $ReposEpreuve->FindLastEpreuve($partie),
            'epreuveEtat'   => '',

            'timeZone' => new \DateTimeZone('Europe/Paris'),
            'maintenant' => new DateTimeImmutable(),

            'dureeEpreuve' => '',
            'dureeRestanteAvantDebutEpreuve' => 0,
            'dureeRestanteAvantFinEpreuve' => 0,
            'dureeRestanteAvantDebutNotation' => $DUREE_TRANSITION * 2,  //Temps de soumettre formulaire Epreuve + 1* Duree_transition
            'dureeRestanteAvantFinNotation' => 0,
            'dureeNotation'   => 0,

            'epreuveDateDebut' => new DateTimeImmutable('3000-01-01'),
            'epreuveDateFin' => new DateTimeImmutable('3000-01-01'),
            'epreuveDateDebutNotation' => new DateTimeImmutable('3000-01-01'),
            'epreuveDateFinNotation' => new DateTimeImmutable('3000-01-01'),

            'nbreJoueurs' =>  $ReposJoueurs->countJoueurByIdpartie($partie),
            'nbreRedaction' => 0,

            //Flag pour accélérer traitement
            'FlagRedacsTerminees' => 'false',
            'FlagNotationsTerminees'=> 'false'
        ];

        //Récupération du nombre de joueurs ayant déjà rédigés leur rédaction
        $nbreJoueursAyantTerminesEpreuve = 0;
        $nbreJoueursAyantTerminesEpreuve =  $ReposRedaction->getNumberPlayerWhoCompletedTheEvent( $tabInfos['partieEpreuve']);

        //Récupération du nombre de joueurs ayant déjà notés l'épreuve
        $nbreNoteEpreuve = 0;
        $nbreNoteEpreuve =  $ReposNotation->getNumberNoteForTheEvent($tabInfos['partieEpreuve'], $tabInfos['nbreJoueurs'], $ReposRedaction ); 

        $tabInfos['FlagRedacsTerminees'] = ($nbreJoueursAyantTerminesEpreuve === $tabInfos['nbreJoueurs'] ? 'true' : 'false');
        $tabInfos['FlagNotationsTerminees'] = ($nbreNoteEpreuve === ($tabInfos['nbreJoueurs'] * ($tabInfos['nbreJoueurs'] - 1))  ? 'true' : 'false');

        $tabInfos['epreuveEtat'] = $tabInfos['partieEpreuve']->getEtatEpreuve()->getNom();
                
        $tabInfos['maintenant'] = $tabInfos['maintenant']->setTimezone($tabInfos['timeZone']);
        $tabInfos['epreuveDateDebut'] = $tabInfos['partieEpreuve']->getDateDebutEpreuve();
        $tabInfos['epreuveDateDebut'] = $tabInfos['epreuveDateDebut']->setTimezone($tabInfos['timeZone']);
        $tabInfos['dureeEpreuve'] = $ReposSousCategorie->retournerSousCategorieEpreuve($tabInfos['partieEpreuve']->getSousCategorie())->getDureeParDefaut() * 60; //en secondes

        $tempFinEpreuve = $tabInfos['partieEpreuve']->getDateFinEpreuve();
        if ($tempFinEpreuve != null)
        {
            $tabInfos['epreuveDateFin'] =  $tempFinEpreuve;
        }
        else
        {
            $tabInfos['epreuveDateFin'] = $tabInfos['epreuveDateDebut']->modify("+{$tabInfos['dureeEpreuve']} seconds");
        }
       
        $tabInfos['nbreRedaction'] = $tabInfos['nbreJoueurs'];
        $tabInfos['dureeNotation'] = (($tabInfos['nbreRedaction'] - 1) * ($tabInfos['dureeEpreuve'] / 4)) + 8; // La notation d'une rédaction dure 4 fois moins longtemps qu'une rédaction. 8 => Temps de chargement On ne note pas sa rédaction;
       
        $tabInfos['epreuveDateDebutNotation'] = $tabInfos['epreuveDateFin']->modify("+{$tabInfos['dureeRestanteAvantDebutNotation']} seconds");
        $tabInfos['epreuveDateFinNotation'] = $tabInfos['epreuveDateDebutNotation']->modify("+{$tabInfos['dureeNotation']} seconds");
                

        //Lancement des calculs
        //Calcul de la durée restante avant début de l'épreuve
        if ( $tabInfos['maintenant'] < $tabInfos['epreuveDateDebut'])
        {
            $intervalTemp = $tabInfos['epreuveDateDebut']->diff($tabInfos['maintenant']);
            $tabInfos['dureeRestanteAvantDebutEpreuve'] = ($intervalTemp->days * 24 * 60 * 60) + // Convertir les jours en secondes
                                                     ($intervalTemp->h * 60 * 60) +         // Convertir les heures en secondes
                                                     ($intervalTemp->i * 60) +              // Convertir les minutes en secondes
                                                     $intervalTemp->s;
        }
        else
        {
            $tabInfos['dureeRestanteAvantDebutEpreuve'] = 0;
        }

        //Calcul de la durée restante avant fin de l'épreuve
        if ($tabInfos['maintenant'] <  $tabInfos['epreuveDateFin'])
        {
            $intervalTemp = $tabInfos['epreuveDateFin']->diff($tabInfos['maintenant']);
            $tabInfos['dureeRestanteAvantFinEpreuve'] = ($intervalTemp->days * 24 * 60 * 60) + // Convertis les jours en secondes
                                                     ($intervalTemp->h * 60 * 60) +            // Convertis les heures en secondes
                                                     ($intervalTemp->i * 60) +                 // Convertis les minutes en secondes
                                                     $intervalTemp->s;
        }
        
        //Calcul de la durée restante avant le début de la notation
        if ($tabInfos['maintenant'] <  $tabInfos['epreuveDateDebutNotation'])
        {
            $intervalTemp = $tabInfos['epreuveDateDebutNotation']->diff($tabInfos['maintenant']);
            $tabInfos['dureeRestanteAvantDebutNotation'] = ($intervalTemp->days * 24 * 60 * 60) + // Convertis les jours en secondes
                                          ($intervalTemp->h * 60 * 60) +                          // Convertis les heures en secondes
                                          ($intervalTemp->i * 60) +                               // Convertis les minutes en secondes
                                          $intervalTemp->s;
        }

        //Calcul de la durée restante avant la fin de la notation
        if ($tabInfos['maintenant'] <  $tabInfos['epreuveDateFinNotation'])
        {
            $intervalTemp = $tabInfos['epreuveDateFinNotation']->diff($tabInfos['maintenant']);
            $tabInfos['dureeRestanteAvantFinNotation'] = ($intervalTemp->days * 24 * 60 * 60) +    // Convertis les jours en secondes
                                          ($intervalTemp->h * 60 * 60) +                           // Convertis les heures en secondes
                                          ($intervalTemp->i * 60) +                                // Convertis les minutes en secondes
                                          $intervalTemp->s;
        }
        
        return $tabInfos;
    }
   

    //Lorsque le FrontEnd le contacte, envoie des informations au FrontEnd (pour les épreuves, les notations et la salle d'attente)
    #[Route('/envoi_info_frontend/{idPartie}/{typeInfo}/{numeroEpreuve}/{epreuveEtat}/{codeConnexion}/{FlagRedacsTerminees}/{FlagNotationsTerminees}', name: 'envoi_info_frontend')] //, methods:"POST"
    public function EnvoiInfoFrontEnd(  Request $request,
                                                EntityManagerInterface $em,  
                                                PartieRepository $ReposPartie, 
                                                PartieJoueurRepository $ReposJoueurs, PartieEpreuveRepository $ReposEpreuve, 
                                                SousCategorieEtapeRepository $ReposSousCategorie,
                                                EpreuveEtatRepository $ReposEpreuveEtat,
                                                RedactionRepository $ReposRedaction,
                                                PartieEtatRepository $ReposEtat, 
                                                NotationRepository $ReposNotation,
                                                SerializerInterface $serializer, 
                                                
                                                int $idPartie = -1,
                                                $typeInfo = "",
                                                $numeroEpreuve,
                                                $epreuveEtat,
                                                $codeConnexion,
                                                $FlagRedacsTerminees,
                                                $FlagNotationsTerminees
                                            ): JsonResponse
    {
        $retour = "";

        //---------------------------
        //Vérifications de sécurités|
        //---------------------------
        if ($idPartie > 0)
        {
            $partie = $ReposPartie->find($idPartie);
        }

        else
        {
            $retour = "Information incorrecte idPartie.";
            $jsonRetour = $serializer->serialize($retour, 'json');
            return new JsonResponse($jsonRetour, Response::HTTP_OK, [], true);
        }

        //Récupération de la partie grâce au code de connexion.
        if (empty($partie))
        {
            $retour = "Information incorrecte idPartie.";
            $jsonRetour = $serializer->serialize($retour, 'json');
            return new JsonResponse($jsonRetour, Response::HTTP_OK, [], true);
        }

        //Récupération de l'utilisateur
        $user = $this->getUser();
        if (!$user) 
        {
            $retour = "Information incorrecte user.";
            $jsonRetour = $serializer->serialize($retour, 'json');
            return new JsonResponse($jsonRetour, Response::HTTP_OK, [], true);
        }

        //Vérifie que l'utilisateur est inscrit dans cette partie
        if(!$ReposJoueurs->JoueurDejaEnregistreDansPartie($user->getId(), $partie->getId()))
        {
            $retour = "Information incorrecte user par dans partie.";
            $jsonRetour = $serializer->serialize($retour, 'json');
            return new JsonResponse($jsonRetour, Response::HTTP_OK, [], true);
        }
          
        //---------------------------
        //Recherche du temps restant|
        //---------------------------
        if ($retour === "")
        {
            $tabInfos = $this->EnvoiInfosSurPartie(
                                                    $ReposEpreuve,                                     
                                                    $ReposJoueurs, 
                                                    $ReposSousCategorie,
                                                    $ReposRedaction,
                                                    $ReposNotation,
                                                    $partie );

            if ( $typeInfo === "salleAttenteDebutEpreuve") //Le front end a demandé la durée restante d'une épreuve.
            {
                //Si l'épreuve est en cours
                if ( $tabInfos['epreuveEtat'] !== $this->getParameter("CONST_ETAT_EPREUVE__VA_DEMARRER"))
                {
                    $erreur = "Information incorrecte. Epreuve pas l'état va démarrer";
                }

                else
                {
                    $erreur = "" ;
                }
            }
            else if ( $typeInfo === "epreuve" || $typeInfo === "salleAttenteFinEpreuve") //Le front end a demandé la durée restante d'une épreuve.
            {
                //Si l'épreuve est en cours
                if ( $tabInfos['epreuveEtat'] !== $this->getParameter("CONST_ETAT_EPREUVE__EN_COURS"))
                {
                    $erreur = "Information incorrecte. Epreuve pas en cours";
                }

                else
                {
                    $erreur = "" ;
                }
            }

            else if( $typeInfo === "salleAttenteDebutNotation")
            {
                if ( $tabInfos['epreuveEtat'] !== $this->getParameter("CONST_ETAT_EPREUVE__NOTATION_VA_DEMARRER"))
                {
                    $erreur = "Information incorrecte. Epreuve pas dans l'état NOTATION_VA_DEMARRER";
                }

                else
                {
                    $erreur = "" ;
                }
            }

            else  //Le front end a demandé la durée restante d'une notation.
            {
                //Si l'épreuve est terminée
                if ( $tabInfos['epreuveEtat'] !== $this->getParameter("CONST_ETAT_EPREUVE__NOTATION_DEMARREE")  )
                     
                {
                    $erreur = "Information incorrecte. Epreuve pas dans l'état notation_demarree";
                }

                else
                {
                    $erreur  = "";
                }
            }
        
            return $this->json([
                'numeroEpreuve' => $tabInfos['partieEpreuve']->getNumEtape(), 
                'epreuveEtat' => $tabInfos['epreuveEtat'] ,

                'dureeRestanteAvantDebutEpreuve' => $tabInfos['dureeRestanteAvantDebutEpreuve'],
                'dureeRestanteAvantFinEpreuve' => $tabInfos['dureeRestanteAvantFinEpreuve'],
                'dureeRestanteAvantDebutNotation' => $tabInfos['dureeRestanteAvantDebutNotation'],
                'dureeRestanteAvantFinNotation' => $tabInfos['dureeRestanteAvantFinNotation'],

                'FlagRedacsTerminees' => $tabInfos['FlagRedacsTerminees'],
                'FlagNotationsTerminees'=> $tabInfos['FlagNotationsTerminees'],

                'erreur' => $erreur,

                'typeInfo' => $typeInfo
            ]);
        }
    }
}