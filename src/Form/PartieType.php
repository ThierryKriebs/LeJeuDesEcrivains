<?php

namespace App\Form;

use App\Entity\GenreLitteraire;
use App\Entity\LongueurPartie;
use App\Entity\Partie;
use App\Entity\PartieEtat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code_connexion')
            ->add('date_creation', null, [
                'widget' => 'single_text',
            ])
            ->add('longueur_partie', EntityType::class, [
                'class' => LongueurPartie::class,
                'choice_label' => 'id',
            ])
            ->add('genre_litteraire', EntityType::class, [
                'class' => GenreLitteraire::class,
                'choice_label' => 'id',
            ])
            ->add('etat', EntityType::class, [
                'class' => PartieEtat::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partie::class,
        ]);
    }
}
