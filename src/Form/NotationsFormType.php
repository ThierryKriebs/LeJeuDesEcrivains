<?php

namespace App\Form;

use App\Entity\Notation;
use App\Entity\redaction;
use App\Entity\utilisateurs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotationsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $noteur = $options['noteur'];

        $builder
            ->add('enfants', CollectionType::class, [
                'entry_type' => NotationFormType::class,
                'by_reference' => true,
                'entry_options' =>[
                    'noteur' => $noteur,
                ]
            ])
            ->add('Envoyer', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,  //Formulaire parent sans entité associée 
            'noteur' => null,

            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'CSRF_notation_LNDJDL__NNN+3',
        ]);
    }
}
