<?php

namespace App\Controller\Admin;

use App\Entity\CategorieEtape;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class CategorieEtapeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategorieEtape::class;
    }

    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInSingular('Catégorie d`épreuve')//Titre des boutons
        ->setEntityLabelInPlural('Catégories d\'épreuves')//Titre en haut de la page
        ->setDefaultSort(['nom' => 'ASC'])
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnIndex()
            ->hideOnForm();

        yield TextField::new('nom', 'Nom de la catégorie')
            ->setSortable(true)
            ->formatValue(function ($value) {
             return is_null($value) ? " " : $value;
         });

        yield TextEditorField::new('Explication')
            ->setSortable(false)
            ->formatValue(function ($value) {
             return is_null($value) ? " " : $value;
         });
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('nom');
    }
}
