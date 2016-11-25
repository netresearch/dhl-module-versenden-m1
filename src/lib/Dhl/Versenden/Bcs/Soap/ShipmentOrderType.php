<?php

namespace Dhl\Versenden\Bcs\Soap;

class ShipmentOrderType
{

    /**
     * @var SequenceNumber $sequenceNumber
     */
    protected $sequenceNumber = null;

    /**
     * @var Shipment $Shipment
     */
    protected $Shipment = null;

    /**
     * @var Serviceconfiguration $PrintOnlyIfCodeable
     */
    protected $PrintOnlyIfCodeable = null;

    /**
     * @var labelResponseType $labelResponseType
     */
    protected $labelResponseType = null;

    /**
     * @param SequenceNumber $sequenceNumber
     * @param Shipment $Shipment
     */
    public function __construct($sequenceNumber, $Shipment)
    {
      $this->sequenceNumber = $sequenceNumber;
      $this->Shipment = $Shipment;
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
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentOrderType
     */
    public function setSequenceNumber($sequenceNumber)
    {
      $this->sequenceNumber = $sequenceNumber;
      return $this;
    }

    /**
     * @return Shipment
     */
    public function getShipment()
    {
      return $this->Shipment;
    }

    /**
     * @param Shipment $Shipment
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentOrderType
     */
    public function setShipment($Shipment)
    {
      $this->Shipment = $Shipment;
      return $this;
    }

    /**
     * @return Serviceconfiguration
     */
    public function getPrintOnlyIfCodeable()
    {
      return $this->PrintOnlyIfCodeable;
    }

    /**
     * @param Serviceconfiguration $PrintOnlyIfCodeable
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentOrderType
     */
    public function setPrintOnlyIfCodeable($PrintOnlyIfCodeable)
    {
      $this->PrintOnlyIfCodeable = $PrintOnlyIfCodeable;
      return $this;
    }

    /**
     * @return labelResponseType
     */
    public function getLabelResponseType()
    {
      return $this->labelResponseType;
    }

    /**
     * @param labelResponseType $labelResponseType
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentOrderType
     */
    public function setLabelResponseType($labelResponseType)
    {
      $this->labelResponseType = $labelResponseType;
      return $this;
    }

}
