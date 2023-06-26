<?php

namespace App\Controller\App;

use App\Entity\Calling\Calling;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CallingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calling::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('numberCalling'),
           // TextField::new('title'),
            TextField::new('name'),
            TextField::new('address'),
            TextField::new('status'),
            TextField::new('description'),
            TextField::new('chronicDiseases'),
            TextField::new('nosology'),
            TextField::new('age'),
            TextField::new('leadType'),
            TextField::new('partnerName'),
            TextField::new('rejectedComment'),
            DateTimeField::new('createdAt'),
          //  DateTimeField::new('acceptedAt'),
          //  DateTimeField::new('completedAt'),
            AssociationField::new('admin'),
            AssociationField::new('doctor'),
            IntegerField::new('price'),
            IntegerField::new('estimated'),
            IntegerField::new('prepayment'),
            IntegerField::new('coastHospital'),
           // TextField::new('price'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->disable(Action::BATCH_DELETE)
        ;
        return $actions;
    }
}
