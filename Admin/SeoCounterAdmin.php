<?php

namespace Artsofte\SeoBundle\Admin;

use ItBlaster\SeoBundle\Model\SeoFile;
use Propel\PropelBundle\Validator\Constraints\UniqueObject;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\Validator\Constraints\Regex;

class SeoCounterAdmin extends Admin
{
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('Title', null, array(
                'label' => 'Название'
            ))
            ->add('CreatedAt', null, array(
                'label'  => 'Создано',
                'format' => 'd.m.Y'
            ))
            ->add('UpdatedAt', null, array(
                'label'  => 'Изменено',
                'format' => 'd.m.Y'
            ))
            ->add('_action', 'actions', array(
                'label'    => 'Действия',
                'sortable' => FALSE,
                'actions'  => array(
                    'edit'   => array(),
                    'delete' => array()
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('Title', null, array(
                'label' => 'Описание'
            ))
            ->add('Content', null, array(
                'label' => 'Содержимое'
            ));
    }
}
