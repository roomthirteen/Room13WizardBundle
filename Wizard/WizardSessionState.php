<?php

namespace Room13\WizardBundle\Wizard;

use Room13\WizardBundle\Exception\InvalidStateException;

class WizardSessionState implements \Serializable
{
    /**
     * @var object Domain object that is the subject of the wizard, must be serializable
     */
    private $subject;

    /**
     * @var integer index of the wizards current step
     */
    private $currentStep;

    /**
     * @var string the uique id of the wizard session state
     */
    private $id;

    private $serializedProperties = array('id','currentStep','subject');

    public function __construct($id)
    {
        $this->id           = $id;
        $this->currentStep  = 0;
        $this->subject      = null;
    }

    public function serialize()
    {
        $data = array();

        foreach($this->serializedProperties as $key)
        {
            $data[$key] = $this->$key;
        }

        return serialize($data);
    }

    public function unserialize($raw)
    {
        // unserialize state
        $data = unserialize($raw);

        foreach($this->serializedProperties as $key)
        {
            if(!isset($data[$key]))
            {
                throw new InvalidStateException(sprintf(
                    'Corrupted wizard session state. Missing property "%s".',
                    $key
                ));
            }

            $this->$key = $data[$key];
        }

        return true;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $currentStep
     */
    public function setCurrentStep($currentStep)
    {
        $this->currentStep = $currentStep;
    }

    /**
     * @return int
     */
    public function getCurrentStep()
    {
        return $this->currentStep;
    }

    /**
     * @param object $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return object
     */
    public function getSubject()
    {
        return $this->subject;
    }

}
