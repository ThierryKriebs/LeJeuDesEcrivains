<?php

namespace App\Form;

use App\Entity\PartieEpreuve;
use App\Entity\Redaction;
use App\Entity\Utilisateurs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RedactionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $joueur = $options['joueur'];
        $partieEpreuve = $options['partieEpreuve'];

        
        $builder
            ->add('redaction', TextareaType::class, [
                'attr' => [
                            'rows' => 11, // Définir la hauteur en nombre de lignes 15 au début
                          ],
            ])
            ->add('score')
            ->add('partieEpreuve', EntityType::class, [
                'class' => PartieEpreuve::class,
                'choice_label' => 'id',
            ])
            ->add('joueur', EntityType::class, [
                'class' => Utilisateurs::class,
                'choice_label' => 'id',
            ])

            ->add('Envoyer', SubmitType::class);

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($joueur, $partieEpreuve)
            {
                $donneesSoumises = $event->getData();
                
                $donneesSoumises['joueur'] = $joueur;
                $donneesSoumises['partieEpreuve'] = $partieEpreuve;
                $event->setData($donneesSoumises);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Redaction::class,

            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'CSRF_redaction_LJDL__EEEE*3',

            'joueur' => null,
            'partieEpreuve' => null,
        ]);
    }
}