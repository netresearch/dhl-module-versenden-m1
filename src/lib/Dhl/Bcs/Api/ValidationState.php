<?php

namespace Dhl\Bcs\Api;

class ValidationState
{

    /**
     * @var SequenceNumber $sequenceNumber
     */
    protected $sequenceNumber = null;

    /**
     * @var Statusinformation $Status
     */
    protected $Status = null;

    /**
     * @param SequenceNumber $sequenceNumber
     * @param Statusinformation $Status
     */
    public function __construct($sequenceNumber, $Status)
    {
      $this->sequenceNumber = $sequenceNumber;
      $this->Status = $Status;
    }

    /**
     * @return SequenceNumber
     */
    public function getSequenceNumber()
    {
      return $this->sequenceNumber;
    }

    /**
     * @param SequenceNumber $sequenceNumber
     * @return \Dhl\Bcs\Api\ValidationState
     */
    public function setSequenceNumber($sequenceNumber)
    {
      $this->sequenceNumber = $sequenceNumber;
      return $this;
    }

    /**
     * @return Statusinformation
     */
    public function getStatus()
    {
      return $this->Status;
    }

    /**
     * @param Statusinformation $Status
     * @return \Dhl\Bcs\Api\ValidationState
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

}
