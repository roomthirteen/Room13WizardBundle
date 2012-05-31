<?php

namespace Room13\WizardBundle\Wizard;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class WizardManager
{
    const STATE_UNKNOWN     = -1;
    const STATE_NEW         = 0;
    const STATE_PROCESSING  = 1;
    const STATE_DONE        = 2;

    /**
     * @var array Wizard objects
     */
    private $wizards;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Router
     */
    private $router;

    function __construct(Session $session, FormFactory $formFactory, Router $router)
    {
        $this->session      = $session;
        $this->formFactory  = $formFactory;
        $this->router       = $router;
        $this->wizards      = array();
    }

    public function addWizard($name,WizardInterface $wizard)
    {
        $wizard->setName($name);

        $this->wizards[$name]=$wizard;
    }

    public function getWizard($name)
    {
        return $this->wizards[$name];
    }

    public function setupWizard($id, WizardInterface $wizard)
    {
        $identifier     = $wizard->getName().'.'.$id;
        $state          = self::STATE_UNKNOWN;
        $sessionState   = null;

        // load or create the session state
        if($this->session->has($identifier))
        {
            // session state already exists, so deserialize it
            $sessionState = $this->session->get($identifier);
            $state        = self::STATE_PROCESSING;

        }
        else
        {
            // wizard has been started just now, so create new state
            $sessionState   = new WizardSessionState($id);
            $state          = self::STATE_NEW;

            // persist the state in the session
            $this->session->set($identifier,$sessionState);
        }

        $wizard->setup($sessionState,$this->formFactory,$this->router);

        return $state;
    }


    public function startWizard(WizardInterface $wizard)
    {
        $wizard->setCurrentStep($wizard->getStep(0));
        $wizard->setSubject($wizard->onStart());
    }

    public function processWizard(WizardInterface $wizard,Request $request)
    {

        // no step specified so redirect
        if(!$request->get('step',false))
        {
            return $wizard->getCurrentUrl();
        }

        // wizard is finished
        if($wizard->getCurrentStep()==='_finished')
        {
            return $wizard->onFinish();
        }

        // a invalid step has been called, so redirect to current
        if($request->get('step',false) && !$wizard->isValidStep($request->get('step',false)))
        {
            return $wizard->getCurrentUrl();
        }

        // check form submisions
        if($request->getMethod() === 'POST')
        {
            $wizard->getForm()->bindRequest($request);

            if($wizard->getForm()->isValid())
            {
                switch($request->get('WizardNextStep','current'))
                {
                    case 'next': $wizard->setCurrentStep($wizard->getNextStep()); break;
                    case 'previous': $wizard->setCurrentStep($wizard->getPreviousStep()); break;
                }

                return $wizard->getCurrentUrl();
            }

        }

        return false;
    }

}
