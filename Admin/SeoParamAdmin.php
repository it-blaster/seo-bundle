<?php

namespace ItBlaster\SeoBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SeoParamAdmin extends Admin
{
    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('url', null, array(
                'label' => 'seo_param_url'
            ))
            ->add('title', 'text', array(
                'label'    => 'seo_param_title',
                'required' => false
            ))
            ->add('keywords', 'textarea', array(
                'label'    => 'seo_param_keywords',
                'required' => false
            ))
            ->add('description', 'textarea', array(
                'label'    => 'seo_param_description',
                'required' => false
            ))
        ;
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('url', 'text', array(
                'label' => 'seo_param_url'
            ))
            ->add('title', 'text', array(
                'label' => 'seo_param_title'
            ))
        ;
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('url', 'text', array(
                'label' => 'seo_param_url'
            ))
            ->add('title', 'text', array(
                'label' => 'seo_param_title'
            ))
            ->add('keywords', 'text', array(
                'label' => 'seo_param_keywords'
            ))
            ->add('description', 'text', array(
                'label' => 'seo_param_description'
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
}