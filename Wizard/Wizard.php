<?php

namespace Room13\WizardBundle\Wizard;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Room13\WizardBundle\Exception\InvalidStateException;

// TODO: nasty namespace bug with ymfony\Bundle\FrameworkBundle\Routing\Router. dunno why
abstract class Wizard implements WizardInterface
{
    /**
     * @var string Unique id of the wizard
     */
    private $id;

    /**
     * @var string The wizards service name
     */
    private $name;

    /**
     * @var WizardSessionStateInterface Current session state of the wizard
     */
    private $state;

    /**
     * @var integer Number of steps of the wizard
     */
    private $numSteps;

    /**
     * Definition of the wizard steps
     * @var array
     */
    private $definitions;


    private $indexStepMap;
    private $stepIndexMap;


    /**
     * @var Form
     */
    private $form;

    /**
     * @var FormView
     */
    private $view;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @var FormFactory
     */
    private $formFactory;



    public function __construct($definitions)
    {

        $this->definitions  = $definitions;

        $this->definitions['_finished']=array(
            'validation_groups'=>array()
        );

        // save metadata
        $this->numSteps     = count($this->definitions)-1;
        $this->indexStepMap = array_keys($this->definitions);
        $this->stepIndexMap = array_flip(array_keys($this->definitions));

    }


    public function setup(WizardSessionState $state, FormFactory $factory, \Symfony\Component\Routing\Router $router)
    {

        $this->state        = $state;
        $this->formFactory  = $factory;
        $this->router       = $router;
    }


    public function buildForm()
    {

        $def = $this->definitions[$this->getCurrentStep()];
        $methodName = 'build'.implode('', array_map('ucfirst', explode('-',$this->getCurrentStep()))).'Form';

        $builder = $this->formFactory->createBuilder('form',$this->getSubject(),array(
            'validation_groups'=>$def['validation_groups'],
        ));

        $this->$methodName($builder);

        $form = $builder->getForm();

        return $form;

    }


    public function getForm()
    {
        if($this->form===null)
        {
            $this->form = $this->buildForm();
        }

        return $this->form;
    }

    public function getFormView()
    {
        if($this->view === null)
        {
            $this->view = $this->getForm()->createView();
        }

        return $this->view;
    }




    public function assertHasState()
    {
        if($this->state === null)
        {
            throw new InvalidStateException('Statefull method called without setting up the wizard.');
        }
    }

    public function isValidStep($step)
    {
        return isset($this->definitions[$step]);
    }


    public function getNextUrl()
    {
        $this->assertHasState();

        return $this->router->generate($this->getRoute(),array(
            'id'=>$this->getId(),
            'step'=>$this->getNextStep(),
        ));
    }

    public function getPreviousUrl()
    {
        $this->assertHasState();

        return $this->router->generate($this->getRoute(),array(
            'id'=>$this->getId(),
            'step'=>$this->getPreviousStep(),
        ));
    }

    public function getCurrentUrl()
    {
        $this->assertHasState();

        return $this->router->generate($this->getRoute(),array(
            'id'=>$this->getId(),
            'step'=>$this->getCurrentStep(),
        ));
    }




    public function setSubject($subject)
    {
        $this->assertHasState();
        $this->state->setSubject($subject);
    }


    public function setName($name)
    {
        $this->name = $name;
    }


    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        $this->assertHasState();

        return $this->state->getId();
    }


    public function getNumSteps()
    {
        return $this->numSteps;
    }

    public function getCurrentStep()
    {
        $this->assertHasState();

        return $this->state->getCurrentStep();
    }

    public function getCurrentStepIndex()
    {
        $this->assertHasState();

        return $this->stepIndexMap[$this->state->getCurrentStep()];
    }

    public function getNextStep()
    {
        $index = $this->stepIndexMap[$this->getCurrentStep()];

        // TODO: bounds checking

        if($index>=count($this->stepIndexMap)-1)
        {
            return 'finish';
        }

        $nextStep = $this->indexStepMap[$index+1];

        return $nextStep;
    }

    public function getPreviousStep()
    {
        $index = $this->stepIndexMap[$this->getCurrentStep()];

        // TODO: bounds checking

        if($index===0)
        {
            return $this->indexStepMap[0];
        }

        $nextStep = $this->indexStepMap[$index-1];

        return $nextStep;
    }


    public function isFinished()
    {
        return $this->getCurrentStep()==='_finished';
    }

    public function setCurrentStep($step)
    {
        $this->assertHasState();

        return $this->state->setCurrentStep($step);
    }

    public function getSubject()
    {
        $this->assertHasState();

        return $this->state->getSubject();
    }

    public function getStep($index)
    {
        return $this->indexStepMap[$index];
    }

    public function getSessionState()
    {
        return $this->state;
    }

}
