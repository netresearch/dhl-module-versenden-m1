<?php

namespace Dhl\Versenden\Bcs\Soap;

class CreationState
{

    /**
     * @var SequenceNumber $sequenceNumber
     */
    protected $sequenceNumber = null;

    /**
     * @var LabelData $LabelData
     */
    protected $LabelData = null;

    /**
     * @param SequenceNumber $sequenceNumber
     * @param LabelData $LabelData
     */
    public function __construct($sequenceNumber, $LabelData)
    {
      $this->sequenceNumber = $sequenceNumber;
      $this->LabelData = $LabelData;
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
     * @return \Dhl\Versenden\Bcs\Soap\CreationState
     */
    public function setSequenceNumber($sequenceNumber)
    {
      $this->sequenceNumber = $sequenceNumber;
      return $this;
    }

    /**
     * @return LabelData
     */
    public function getLabelData()
    {
      return $this->LabelData;
    }

    /**
     * @param LabelData $LabelData
     * @return \Dhl\Versenden\Bcs\Soap\CreationState
     */
    public function setLabelData($LabelData)
    {
      $this->LabelData = $LabelData;
      return $this;
    }

}
