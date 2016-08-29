<?php

namespace Dhl\Bcs\Api;

class ErrorType
{

    /**
     * @var int $priority
     */
    protected $priority = null;

    /**
     * @var int $code
     */
    protected $code = null;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var string $descriptionLong
     */
    protected $descriptionLong = null;

    /**
     * @var string $solution
     */
    protected $solution = null;

    /**
     * @var string $application
     */
    protected $application = null;

    /**
     * @var string $module
     */
    protected $module = null;

    /**
     * @param int $code
     * @param \DateTime $dateTime
     */
    public function __construct($code, \DateTime $dateTime)
    {
      $this->code = $code;
      $this->dateTime = $dateTime->format(\DateTime::ATOM);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
      return $this->priority;
    }

    /**
     * @param int $priority
     * @return \Dhl\Bcs\Api\ErrorType
     */
    public function setPriority($priority)
    {
      $this->priority = $priority;
      return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
      return $this->code;
    }

    /**
     * @param int $code
     * @return \Dhl\Bcs\Api\ErrorType
     */
    public function setCode($code)
    {
      $this->code = $code;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
      if ($this->dateTime == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->dateTime);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $dateTime
     * @return \Dhl\Bcs\Api\ErrorType
     */
    public function setDateTime(\DateTime $dateTime)
    {
      $this->dateTime = $dateTime->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->description;
    }

    /**
     * @param string $description
     * @return \Dhl\Bcs\Api\ErrorType
     */
    public function setDescription($description)
    {
      $this->description = $description;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionLong()
    {
      return $this->descriptionLong;
    }

    /**
     * @param string $descriptionLong
     * @return \Dhl\Bcs\Api\ErrorType
     */
    public function setDescriptionLong($descriptionLong)
    {
      $this->descriptionLong = $descriptionLong;
      return $this;
    }

    /**
     * @return string
     */
    public function getSolution()
    {
      return $this->solution;
    }

    /**
     * @param string $solution
     * @return \Dhl\Bcs\Api\ErrorType
     */
    public function setSolution($solution)
    {
      $this->solution = $solution;
      return $this;
    }

    /**
     * @return string
     */
    public function getApplication()
    {
      return $this->application;
    }

    /**
     * @param string $application
     * @return \Dhl\Bcs\Api\ErrorType
     */
    public function setApplication($application)
    {
      $this->application = $application;
      return $this;
    }

    /**
     * @return string
     */
    public function getModule()
    {
      return $this->module;
    }

    /**
     * @param string $module
     * @return \Dhl\Bcs\Api\ErrorType
     */
    public function setModule($module)
    {
      $this->module = $module;
      return $this;
    }

}
