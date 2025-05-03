<?php

namespace App\Form;

use App\Entity\Notation;
use App\Entity\redaction;
use App\Entity\utilisateurs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

use Symfony\Component\Form\Extension\Core\Type\RangeType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class NotationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $noteur = $options['noteur'];
        
                
        $builder
            ->add('note', )

            ->add('note', RangeType::class, [
                'attr' => [
                    'min' => 0, // Empêche LA SAISIE de valeurs négatives
                    'max' => 20,
                ],
                'empty_data' => 20,
                'data' => 20,
                'help' => 'Entrer une note pour la rédaction (entre 0 et 20)',
            ])
            
            ->add('remarque',TextareaType::class,[
                'help' => 'Entrer une remarque (optionnel)',
                'required' => false,
            ])
            
            //Utilisera la méthode __toString pour obtenir un string de l'objet redaction
            ->add('redaction', TextareaType::class, [
                'disabled' => true,
                'attr' => [
                    'readonly' => true,
                    'rows' => 8, // Définir la hauteur en nombre de lignes
                ],
            ])

            ->add('noteur', EntityType::class, [
                'class' => utilisateurs::class,
                'choice_label' => 'id',
            ])
            ;

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($noteur)
            {
                $donneesSoumises = $event->getData();
                $donneesSoumises['noteur'] = $noteur;
                $event->setData($donneesSoumises);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notation::class,
          
             'noteur' => null,
        ]);
    }
}
