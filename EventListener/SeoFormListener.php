<?php

namespace ItBlaster\SeoBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Form;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\Translator;
use ItBlaster\SeoBundle\Model\SeoParam;
use ItBlaster\SeoBundle\Model\SeoParamQuery;
use ItBlaster\SeoBundle\Model\SeoParamI18n;

class SeoFormListener
{
    /**
     * Object of GetResponseEvent
     * @var GetResponseEvent
     */
    protected $event = null;

    /**
     * Object of Session
     * @var Session
     */
    protected $session;

    /**
     * Object of Form
     * @var Form
     */
    protected $seo_form = null;

    /**
     * Object of EngineInterface
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Object of SecurityContextInterface
     * @var SecurityContextInterface
     */
    protected $security_context;

    /**
     * Object of Translator
     * @var Translator
     */
    protected $translator;

    /**
     * Array of allowed roles
     * @var array
     */
    protected $allowed_roles = array();

    /**
     * Object of SeoParam
     * @var SeoParam
     */
    protected $seo_param = null;

    /**
     * @param Form                     $seo_form
     * @param Session                  $session
     * @param EngineInterface          $templating
     * @param SecurityContextInterface $securityContext
     * @param Translator               $translator
     * @param array                    $allowed_roles
     */
    public function __construct(Form $seo_form, Session $session, EngineInterface $templating, SecurityContextInterface $securityContext, Translator $translator, array $allowed_roles)
    {
        $this->seo_form         = $seo_form;
        $this->session          = $session;
        $this->templating       = $templating;
        $this->security_context = $securityContext;
        $this->allowed_roles    = $allowed_roles;
        $this->translator       = $translator;
    }

    /**
     * Method handles the form
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request   = $event->getRequest();
        $form_name = $this->seo_form->getName();
        if (!$request->request->has($form_name)) {
            return;
        }

        if (!$this->security_context->isGranted($this->allowed_roles)) {
            throw new AccessDeniedException();
        }

        $this->event = $event;
        $this->seo_form->setData($this->getSeoParam());

        $response = null;
        $success  = $this->process();
        if ($request->isXmlHttpRequest()) {
            $form = $this->seo_form;
            $renderedForm = $this->templating->render('ItBlasterSeoBundle:Seo:form.html.twig', array(
                'form'      => $form->createView(),
                'form_name' => $form->getName()
            ));

            $response = new JsonResponse(
                array(
                    'success' => $success,
                    'form'    => $renderedForm
                )
            );
        } else {
            if ($success) {
                $flashBag = $this->session->getFlashBag();
                $flashBag->add($form_name, $this->translator->trans('seo_form_listener_changes_saved'));

                $response = new RedirectResponse($request->getUri());
            }
        }

        if ($response) {
            $event->setResponse($response);
        }
    }

    /**
     * Does save or delete record in the database
     *
     * @return bool
     */
    protected function process()
    {
        $seo_param = $this->getSeoParam();
        $request   = $this->event->getRequest();
        $seo_form  = $this->seo_form;

        $seo_form->submit($request);
        if ($seo_form->isValid()) {
            // If no data - delete record
            /** @type SeoParam $data */
            $data = $seo_form->getData();

            $save = false;

            foreach ($data->getSeoParamI18ns() as $seoParamTranslation) {
                if (!$seoParamTranslation->getTitle() && !$seoParamTranslation->getKeywords() && !$seoParamTranslation->getDescription()) {
                    $seoParamTranslation->delete();

                } else {
                    $seoParamTranslation->save();
                    $save = true;
                }
            }

            if ($save) {
                $data->save();
            } else {
                $data->delete();
            }

            return true;
        }

        return false;
    }

    /**
     * Returns object of Form
     *
     * @return Form
     */
    protected function getSeoParam()
    {
        if ($this->seo_param === null)
        {
            $request = $this->event->getRequest();
            $form_name = $this->seo_form->getName();
            $request_data = $request->request->get($form_name);

            $url = isset($request_data['url']) ? $request_data['url'] : null;

            $this->seo_param = $url && ($seo_param = SeoParamQuery::create()->filterByUrl($url, \Criteria::EQUAL)->findOne()) ?
                $seo_param :
                new SeoParam();
        }

        return $this->seo_param;
    }
}