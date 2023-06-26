<?php

namespace App\Controller\App;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new(propertyName: 'phone'),
            TextField::new(propertyName: 'name'),
            ChoiceField::new(propertyName: 'roles')
                ->setChoices([
                    'Руководитель'    => 'ROLE_SUPER_ADMIN',
                    'Администратор' => 'ROLE_ADMIN',
                    'Доктор' => 'ROLE_DOCTOR',
                ])
                ->setRequired(isRequired: false)
                ->allowMultipleChoices()
            ,
            TextField::new(propertyName: 'plainPassword')->onlyOnForms(),
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
