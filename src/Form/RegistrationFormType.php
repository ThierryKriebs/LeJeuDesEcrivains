<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class, [
                'label'=> 'Nom d\'utilisateur',
                //Les contraintes d'unicité ont été mises dans l'entité et ne doivent donc pas figurer ici
            ])
            
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'S\'il vous plaît, entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => $_ENV['MDP_MIN'],
                        'minMessage' => 'Votre mot de passe doit être au moins égal à {{ limit }} charactères',
                        // max length allowed by Symfony for security reasons
                        'max' => $_ENV['MDP_MAX'],
                        'maxMessage' => 'Votre mot de passe doit être inférieur ou égal à {{ limit }} charactères',
                    ]),
                ],
            ])
            
            ->add('email', TextType::class, [
                'label'=> 'E-mail',
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse {{ value }} n\'est pas une adresse email valide',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Accepter les conditions d\'utilisation',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez être d\'accord avec nos conditions d\'utilisation.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
