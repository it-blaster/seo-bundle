<?php

namespace ItBlaster\SeoBundle\Form\Type;

use Symfony\Bridge\Propel1\Form\Type\TranslationCollectionType;
use Symfony\Bridge\Propel1\Form\Type\TranslationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SeoFormType
 * @package ItBlaster\SeoBundle\Form\Type
 */
class SeoFormType extends AbstractType
{
    /**
     * Object of Request
     * @var Request|null
     */
    protected $request;

    protected $locales;

    /**
     * Constructor
     *
     * @param RequestStack $request_stack
     * @param array $locales
     */
    public function __construct(RequestStack $request_stack, array $locales)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $available_urls = $this->getAvailableUrls();
        $choices = array_combine($available_urls, $available_urls);

        $builder->add('url', 'choice', array(
            'label' => 'seo_param_url',
            'choices' => $choices,
            'constraints' => array(
                new Assert\NotBlank()
            ))
        );

        $builder->add('SeoParamI18ns', new TranslationCollectionType(), array(
            'label'     => false,
            'required'  => false,
            'type'      => new TranslationType(),
            'languages' => $this->locales,
            'options' => array(
                'label' => false,
                'data_class' => 'ItBlaster\SeoBundle\Model\SeoParamI18n',
                'columns' => array(
                    'Title' => array(
                        'label' => 'Заголовок',
                        'type' => 'text',
                        'required' => false,
                    ),
                    'Keywords' => array(
                        'label' => 'Ключевые слова',
                        'type' => 'text',
                        'required' => false
                    ),
                    'Description' => array(
                        'label' => 'Описание',
                        'type' => 'text',
                        'required' => false
                    ),
                )
            )
        ));

        $builder->add('Submit', 'submit', array('label' => 'seo_form_type_submit'));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ItBlaster\SeoBundle\Model\SeoParam',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'it_blaster_seo_form';
    }

    /**
     * Returns an array of URL for form
     *
     * @return array
     */
    protected function getAvailableUrls()
    {
        $request      = $this->request;
        $path         = $request instanceof Request ? '/' . trim($request->getPathInfo(), '/') : '/';
        $query_string = $request instanceof Request ? $request->getQueryString() : '';

        return array(
            $path . ($query_string ? '?' . $query_string : ''),
            $path,
            rtrim($path, '/') . '/*'
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $this->locales;
    }
}
