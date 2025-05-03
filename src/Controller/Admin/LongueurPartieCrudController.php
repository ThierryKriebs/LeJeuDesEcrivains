<?php

namespace App\Controller\Admin;

use App\Entity\LongueurPartie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class LongueurPartieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LongueurPartie::class;
    }
   
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
        ->hideOnIndex()
        ->hideOnForm();

        yield TextField::new('nom', 'Etat')
        ->setSortable(true);

        yield IntegerField::new('nombre_etape', 'Nombre d\'étape')
        ->setSortable(true);
    }
}