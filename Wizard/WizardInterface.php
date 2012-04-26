<?php

namespace Room13\WizardBundle\Wizard;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;

interface WizardInterface
{
    /**
     * @abstract
     * @return mixed
     */
    public function getName();

    /**
     * @abstract
     * @param $name
     * @return string The wizards service name.
     */
    public function setName($name);

    /**
     * @abstract
     * @return integer Number of steps in wizard
     */
    public function getNumSteps();

    /**
     * Sets up the wizard. This initializes the wizard object with the wizards current state.
     * @abstract
     * @param WizardSessionState $state
     * @param FormFactory $builder
     * @param Router $router
     * @return mixed
     */
    public function setup(WizardSessionState $state, FormFactory $factory, Router $router);

    /**
     * @abstract
     * @return string The uique id of the wizard.
     */
    public function getId();

    /**
     * @abstract
     * @return integer The current step the wizards state is in.
     */
    public function getCurrentStep();

    public function getCurrentStepIndex();

    public function onFinish();

    /**
     * @abstract
     */
    public function getNextStep();

    /**
     * @abstract
     */
    public function getPreviousStep();

    public function isFinished();
    public function isValidStep($step);


    /**
     * @abstract
     * @return mixed
     */
    public function setCurrentStep($step);

    /**
     * Subject to be used for forms. Must implement Serializable.
     * Entities or Documents should be detached before used as subject.
     * @abstract
     * @param $subject
     * @return mixed
     */
    public function setSubject($subject);

    /**
     * Returns the wizards state subject.
     * @abstract
     * @return mixed
     */
    public function getSubject();

    /**
     * Form of the current wizard state.
     * @abstract
     * @return mixed
     */
    public function getForm();

    /**
     * Form view of the current wizard state.
     * @abstract
     * @return mixed
     */

    public function getFormView();

    /**
     * Builds the wizards current states form. May only be called once per instance.
     * @abstract
     * @return mixed
     */
    public function buildForm();

    /**
     * Returns the wizards base route
     * @abstract
     * @return mixed
     */
    public function getRoute();

    /**
     * gets the url of the active session state
     * @abstract
     * @return mixed
     */
    public function getCurrentUrl();

    /**
     * gets the url of the active session state
     * @abstract
     * @return mixed
     */
    public function getNextUrl();

    /**
     * gets the url of the active session state
     * @abstract
     * @return mixed
     */
    public function getPreviousUrl();


    /**
     * @abstract
     * @return WizardSessionState
     */
    public function getSessionState();


}
