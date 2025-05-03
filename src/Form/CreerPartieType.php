<?php

namespace App\Form;

use App\Entity\GenreLitteraire;
use App\Entity\LongueurPartie;
use App\Entity\Partie;
use App\Entity\PartieEtat;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use App\Repository\GenreLitteraireRepository;
use App\Repository\PartieEtatRepository;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CreerPartieType extends AbstractType
{
    private $genreLitteraireParDefaut;

    public function __construct(private ContainerBagInterface $parameterBag, private PartieEtatRepository $ReposPartieEtat)
    {
     
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $nbreEtapeEnBase = $options['nbreEpreuveMax'] ;
        
        $builder
            ->add('code_connexion')
            ->add('date_creation', null, [
                'widget' => 'single_text',
            ])

            ->add('longueur_partie', EntityType::class, [
                'class' => LongueurPartie::class,

                'choice_label' => function (LongueurPartie $lp) use ($nbreEtapeEnBase): string {
                       
                        $nbreEtapeSelectionneParJoueur = $lp->getNombreEtape();

                        if ($nbreEtapeSelectionneParJoueur > $nbreEtapeEnBase )
                        {
                            $retour = $nbreEtapeEnBase." => ".$lp->getNom();
                        }

                        else
                        {
                            $retour = $nbreEtapeSelectionneParJoueur." => ".$lp->getNom();
                        }

                        return $retour;
                    }    ,

                'label' => 'Nombre d\'épreuve(s) à passer: ',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('long')
                    ->orderBy('long.nombre_etape', 'ASC');
                },
                'help' => 'Indique le nombre d\'épreuve à réaliser pour terminer la partie.',
                'placeholder' => 'Nombre d\'épreuve de la partie :',
                'data' => $options['longueurPartieParDefaut'], //présélection de la valeur par défaut
            ])

            ->add('genre_litteraire', EntityType::class, [
                'class' => GenreLitteraire::class,
                'label' => 'Sélectionner un genre littéraire: ',
                'choice_label' => 'nom',
                'expanded' => true,
                'help' => 'Indique le genre littéraire sur lequel portera les épreuves.',
                'data' => $options['genreLitteraireParDefaut'], //présélection de la valeur par défaut (si pas fait dans le Twig)
            ])

            ->add('etat', EntityType::class, [
                'class' => PartieEtat::class,
                'choice_label' => 'nom',
            ])
            
            ->add('Creer', SubmitType::class)


            ->addEventListener(FormEvents::PRE_SUBMIT, [$this,'soumissionDonneesSupplementaires']);
        ;
    }

    /**
     * Méthode appelée à chaque création de partie
     * Ajout de: la date de création, l'état de la partie... 
     * @param Formevent $event
     * @return void
     */
    public function soumissionDonneesSupplementaires ( Formevent $event)
    {
        $donneesSoumises = $event->getData();

        //Génération du code unique de connexion:
        $donneesSoumises['code_connexion'] = uniqid(''); //strval(99556699777);
                
        //Génération de la date de création de la partie:
        $timeZone = new \DateTimeZone('Europe/Paris');
        $date_actuelle = new DateTimeImmutable();
        $date_actuelle = $date_actuelle->setTimezone($timeZone);
        $donneesSoumises['date_creation'] = date_format( $date_actuelle, 'Y-m-d H:i:s');  // Format identique à la table => "2024-09-03 10:16:01";
        
        $etat = $this->ReposPartieEtat->findIdEtatPartieByNom($this->parameterBag->get("CONST_ETAT_PARTIE__EN_COURS_DE_CONNEXION"));
        $donneesSoumises['etat'] = $etat;

        $event->setData($donneesSoumises);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partie::class,
            
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'CSRF_creer_partie_LJDL__EEEE*3',
            'genreLitteraireParDefaut' => null,
            'longueurPartieParDefaut' => null,
            'nbreEpreuveMax' => null
        ]);
    }
}
