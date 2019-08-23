<?php

namespace Dhl\Versenden\Bcs\Soap;

class CreateShipmentOrderRequest
{

    /**
     * @var Version $Version
     */
    protected $Version = null;

    /**
     * @var ShipmentOrderType $ShipmentOrder
     */
    protected $ShipmentOrder = null;

    /**
     * @var labelResponseType $labelResponseType
     */
    protected $labelResponseType = null;

    /**
     * @var groupProfileName $groupProfileName
     */
    protected $groupProfileName = null;

    /**
     * @var labelFormat $labelFormat
     */
    protected $labelFormat = null;

    /**
     * @var labelFormatRetoure $labelFormatRetoure
     */
    protected $labelFormatRetoure = null;

    /**
     * @var combinedPrinting $combinedPrinting
     */
    protected $combinedPrinting = null;

    /**
     * @var feederSystem $feederSystem
     */
    protected $feederSystem = null;

    /**
     * @param Version $Version
     * @param ShipmentOrderType $ShipmentOrder
     * @param labelResponseType $labelResponseType
     * @param groupProfileName $groupProfileName
     * @param labelFormat $labelFormat
     * @param labelFormatRetoure $labelFormatRetoure
     * @param combinedPrinting $combinedPrinting
     * @param feederSystem $feederSystem
     */
    public function __construct($Version, $ShipmentOrder, $labelResponseType, $groupProfileName, $labelFormat, $labelFormatRetoure, $combinedPrinting, $feederSystem)
    {
      $this->Version = $Version;
      $this->ShipmentOrder = $ShipmentOrder;
      $this->labelResponseType = $labelResponseType;
      $this->groupProfileName = $groupProfileName;
      $this->labelFormat = $labelFormat;
      $this->labelFormatRetoure = $labelFormatRetoure;
      $this->combinedPrinting = $combinedPrinting;
      $this->feederSystem = $feederSystem;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
      return $this->Version;
    }

    /**
     * @param Version $Version
     * @return \Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest
     */
    public function setVersion($Version)
    {
      $this->Version = $Version;
      return $this;
    }

    /**
     * @return ShipmentOrderType
     */
    public function getShipmentOrder()
    {
      return $this->ShipmentOrder;
    }

    /**
     * @param ShipmentOrderType $ShipmentOrder
     * @return \Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest
     */
    public function setShipmentOrder($ShipmentOrder)
    {
      $this->ShipmentOrder = $ShipmentOrder;
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
     * @return \Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest
     */
    public function setLabelResponseType($labelResponseType)
    {
      $this->labelResponseType = $labelResponseType;
      return $this;
    }

    /**
     * @return groupProfileName
     */
    public function getGroupProfileName()
    {
      return $this->groupProfileName;
    }

    /**
     * @param groupProfileName $groupProfileName
     * @return \Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest
     */
    public function setGroupProfileName($groupProfileName)
    {
      $this->groupProfileName = $groupProfileName;
      return $this;
    }

    /**
     * @return labelFormat
     */
    public function getLabelFormat()
    {
      return $this->labelFormat;
    }

    /**
     * @param labelFormat $labelFormat
     * @return \Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest
     */
    public function setLabelFormat($labelFormat)
    {
      $this->labelFormat = $labelFormat;
      return $this;
    }

    /**
     * @return labelFormatRetoure
     */
    public function getLabelFormatRetoure()
    {
      return $this->labelFormatRetoure;
    }

    /**
     * @param labelFormatRetoure $labelFormatRetoure
     * @return \Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest
     */
    public function setLabelFormatRetoure($labelFormatRetoure)
    {
      $this->labelFormatRetoure = $labelFormatRetoure;
      return $this;
    }

    /**
     * @return combinedPrinting
     */
    public function getCombinedPrinting()
    {
      return $this->combinedPrinting;
    }

    /**
     * @param combinedPrinting $combinedPrinting
     * @return \Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest
     */
    public function setCombinedPrinting($combinedPrinting)
    {
      $this->combinedPrinting = $combinedPrinting;
      return $this;
    }

    /**
     * @return feederSystem
     */
    public function getFeederSystem()
    {
      return $this->feederSystem;
    }

    /**
     * @param feederSystem $feederSystem
     * @return \Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest
     */
    public function setFeederSystem($feederSystem)
    {
      $this->feederSystem = $feederSystem;
      return $this;
    }

}
