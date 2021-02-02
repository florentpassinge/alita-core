<?php

declare(strict_types = 1);

namespace Alita\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('email', TextType::class);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('email');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('id', null, [
            'label' => 'ee',
        ]);
        $listMapper->add('email');
    }
}
