<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Validator\Constraints\Image;

use App\Entity\GenreLitteraire;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class GenreLitteraireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GenreLitteraire::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInSingular('Genre littéraire')//Titre des boutons
        ->setEntityLabelInPlural('Genres littéraires')//Titre en haut de la page
        ->setDefaultSort(['nom' => 'ASC'])
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('ID')
            ->hideOnIndex()
            ->hideOnForm();
            
        yield TextField::new('nom', 'Genre')
        ->setSortable(true);
        
        yield TextEditorField::new('Commentaire', 'Définition')
        ->setSortable(false)
        ->formatValue(function ($value) {
            return is_null($value) ? " " : $value;
        });
   
        yield TextEditorField::new('exemple', 'Exemples')
        ->setSortable(false)
        ->formatValue(function ($value) {
            return is_null($value) ? " " : $value;
        });

        yield BooleanField::new('est_active', 'Est actif pour les joueurs')
        ->setSortable(true);

        yield ImageField::new('nom_image', 'Images')
        ->setSortable(false)
        ->setBasePath('images/genres_litteraires/')
        ->setUploadDir('public/images/genres_litteraires/')
        ->setFileConstraints(new Image(maxSize: '1000k'))
        ->setUploadedFileNamePattern('[year]-[month]-[day]-[slug]-[contenthash].[extension]');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('nom')
            ->add('est_active')
            ->add('exemple')
            ->add('Commentaire');
    }
}
