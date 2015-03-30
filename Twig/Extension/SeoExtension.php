<?php

namespace ItBlaster\SeoBundle\Twig\Extension;

use ItBlaster\SeoBundle\Model\SeoCounter;
use ItBlaster\SeoBundle\Model\SeoCounterQuery;
use ItBlaster\SeoBundle\Model\SeoParam;
use ItBlaster\SeoBundle\Service\SeoService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Seo Extension
 * @package ItBlaster\SeoBundle\Twig\Extension
 */

class SeoExtension extends \Twig_Extension
{
    /**
     * Object of SeoService
     * @var SeoService
     */
    protected $seo_service;

    /**
     * Object of Form
     * @var Form
     */
    protected $seo_form;

    /**
     * Object of Request
     * @var Request
     */
    protected $request;

    /**
     * Object of SecurityContextInterface
     * @var SecurityContextInterface
     */
    protected $security_context;

    /**
     * Array of allowed roles
     * @var array
     */
    protected $allowed_roles = array();

    /**
     * Constructor
     *
     * @param SeoService               $seo_service
     * @param Form                     $seo_form
     * @param RequestStack             $request_stack
     * @param SecurityContextInterface $securityContext
     * @param array                    $allowed_roles
     */
    public function __construct(SeoService $seo_service, Form $seo_form, RequestStack $request_stack, SecurityContextInterface $securityContext, array $allowed_roles)
    {
        $this->seo_service      = $seo_service;
        $this->request          = $request_stack->getCurrentRequest();
        $this->seo_form         = $seo_form;
        $this->security_context = $securityContext;
        $this->allowed_roles    = $allowed_roles;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'seoTool' => new \Twig_Function_Method($this, 'seoTool', array(
                'needs_environment' => true,
                'is_safe'           => array('html')
            )),
            'seoCounters' => new \Twig_Function_Method($this, 'seoCounters', array(
                'is_safe'           => array('html')
            ))
        );
    }

    /**
     * Return SEO parameters or render form
     *
     * @param \Twig_Environment $environment
     * @param string            $type          This parameter specifies the output SEO parameters ('title','description','keywords')
     * @param string            $default_value Returned if SEO parameters are not found
     * @return string
     * @throws \RuntimeException Activated if $type is incorrect
     */
    public function seoTool(\Twig_Environment $environment, $type = null, $default_value = null)
    {
        $request = $this->request;

        $current_url = $request->getPathInfo() . (($qs = $request->getQueryString()) ? '?' . $qs : '');
        $seo_param = $this->seo_service->getSeoParamByUrl($current_url);

        if ($type) {
            $value = null;

            if ($seo_param) {
                $method_name = 'get' . ucfirst($type);

                if (method_exists($seo_param, $method_name)) {
                    $value = $seo_param->$method_name();
                } else {
                    throw new \RuntimeException('Type is incorrect.');
                }
            }

            return $value !== null ? $value : $default_value;
        } else {
            if (!$this->security_context->isGranted($this->allowed_roles)) {
                return '';
            }

            if ($seo_param === null) {
                $seo_param = new SeoParam();
            }

            $seo_form = $this->seo_form;
            if (!$seo_form->isSubmitted()){
                $seo_form->setData($seo_param);
            }

            return $environment->render('ItBlasterSeoBundle:Seo:form.html.twig', array(
                'form'      => $seo_form->createView(),
                'form_name' => $seo_form->getName()
            ));
        }
    }

    /**
     * Returns all counters content
     *
     * @return string
     */
    public function seoCounters()
    {
        $counters = SeoCounterQuery::create()->find();
        $result = '';
        /** @var SeoCounter $counter */
        foreach ($counters as $counter) {
            $result .= $counter->getContent();
        }
        return $result;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'seo_extension';
    }
}
