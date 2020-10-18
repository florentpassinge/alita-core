<?php

namespace Alita\Controller\Console;

use Alita\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UserCRUDController.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class UserCRUDController extends BaseCRUDController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('alita.console.title.user')
            ->setEntityLabelInPlural('alita.console.title.users')
        ;

        return parent::configureCrud($crud);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('firstName')
            ->add('email')
            // most of the times there is no need to define the
            // filter type because EasyAdmin can guess it automatically
            ->add(BooleanFilter::new('active'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        switch ($pageName) {
            case 'index':
                return $this->configureFieldsIndex();
            default:
                return [];
        }
    }

    private function configureFieldsIndex(): iterable
    {
        return [
            IdField::new('id', 'alita.entity.id'),
            Field::new('firstName', 'alita.entity.firstName'),
            Field::new('lastName', 'alita.entity.lastName'),
            Field::new('email', 'alita.entity.email'),
            BooleanField::new('active', 'alita.entity.active'),
            ArrayField::new('roles', 'alita.entity.roles'),
            BooleanField::new('blockedAt', 'alita.entity.userBlocked'),
            ];
    }
}