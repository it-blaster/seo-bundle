<?php

namespace ItBlaster\SeoBundle\Admin;

use ItBlaster\SeoBundle\Model\SeoFile;
use Propel\PropelBundle\Validator\Constraints\UniqueObject;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\Validator\Constraints\Regex;

class SeoFileAdmin extends Admin
{
    protected $restricted_files = array(
        'robots.txt'
    );

    public function isGranted($name, $object = NULL)
    {
        if ($object instanceof SeoFile) {
            if ($name == 'DELETE' && in_array($object->getName(), $this->restricted_files)) {
                return FALSE;
            }
        }

        return parent::isGranted($name, $object);
    }

    public function getBatchActions()
    {

    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
        $collection->remove('batch');
        $collection->remove('show');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('Name', null, array(
                'label' => 'Имя файла'
            ))
            ->add('Title', null, array(
                'label' => 'Описание'
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
        /** @var SeoFile $seo_file */
        $seo_file = $this->getSubject();

        if ($seo_file->isNew()) {
            $formMapper
                ->add('Name', null, array(
                    'label' => 'Имя файла'
                ));
        }

        $formMapper
            ->add('Title', null, array(
                'label' => 'Описание'
            ))
            ->add('Content', null, array(
                'label' => 'Содержимое'
            ));
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->addConstraint(new UniqueObject(array(
                'fields'  => 'name',
                'message' => 'Такой файл уже существует'
            )))
            ->with('name')
                ->addConstraint(new Regex(array(
                    'pattern' => '/\.php$|\.htaccess$|\.ico$/',
                    'match' => false,
                    'message' => 'Недопустимое имя файла',
                )))
                ->addConstraint(new Regex(array(
                    'pattern' => '/[a-zA-Z0-9._-]+/',
                    'message' => 'Недопустимое имя файла',
                )))
            ->end()
        ;
    }
}
