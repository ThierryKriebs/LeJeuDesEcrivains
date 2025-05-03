<?php

namespace App\Controller\Admin;

use App\Entity\Partie;
use App\Entity\SousCategorieEtape;
use App\Entity\CategorieEtape;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class SousCategorieEtapeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SousCategorieEtape::class;
    }
     
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInSingular('Sous catégorie d\'épreuve')//Titre des boutons
        ->setEntityLabelInPlural('Sous catégories d\'épreuves')//Titre en haut de la page
        ->setDefaultSort(['nom' => 'ASC'])
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
       yield IdField::new('ID')
        ->hideOnIndex()
        ->hideOnForm();

        yield TextField::new('nom', 'Nom de la sous-catégorie')
            ->setSortable(true)
            ->formatValue(function ($value) {
            return is_null($value) ? " " : $value;
        });

        yield TextEditorField::new('explication', 'Explication/commentaire')
            ->setSortable(false)
            ->formatValue(function ($value) {
            return is_null($value) ? " " : $value;
        });

        yield AssociationField::new('categorieEtape', 'Catégorie');  //Pourquoi faut-il passer l'ID???
        yield IntegerField::new('duree_par_defaut', 'Durée de l\'étape en minutes');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('nom');
    }
}
