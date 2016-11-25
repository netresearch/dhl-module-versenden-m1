<?php

namespace Dhl\Versenden\Bcs\Soap;

class LabelData
{

    /**
     * @var Statusinformation $Status
     */
    protected $Status = null;

    /**
     * @var shipmentNumber $shipmentNumber
     */
    protected $shipmentNumber = null;

    /**
     * @var string $labelUrl
     */
    protected $labelUrl = null;

    /**
     * @var base64Binary $labelData
     */
    protected $labelData = null;

    /**
     * @var string $returnLabelUrl
     */
    protected $returnLabelUrl = null;

    /**
     * @var base64Binary $returnLabelData
     */
    protected $returnLabelData = null;

    /**
     * @var string $exportLabelUrl
     */
    protected $exportLabelUrl = null;

    /**
     * @var base64Binary $exportLabelData
     */
    protected $exportLabelData = null;

    /**
     * @var string $codLabelUrl
     */
    protected $codLabelUrl = null;

    /**
     * @var base64Binary $codLabelData
     */
    protected $codLabelData = null;

    /**
     * @param Statusinformation $Status
     * @param shipmentNumber $shipmentNumber
     */
    public function __construct($Status, $shipmentNumber)
    {
      $this->Status = $Status;
      $this->shipmentNumber = $shipmentNumber;
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
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return shipmentNumber
     */
    public function getShipmentNumber()
    {
      return $this->shipmentNumber;
    }

    /**
     * @param shipmentNumber $shipmentNumber
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setShipmentNumber($shipmentNumber)
    {
      $this->shipmentNumber = $shipmentNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getLabelUrl()
    {
      return $this->labelUrl;
    }

    /**
     * @param string $labelUrl
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setLabelUrl($labelUrl)
    {
      $this->labelUrl = $labelUrl;
      return $this;
    }

    /**
     * @return base64Binary
     */
    public function getLabelData()
    {
      return $this->labelData;
    }

    /**
     * @param base64Binary $labelData
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setLabelData($labelData)
    {
      $this->labelData = $labelData;
      return $this;
    }

    /**
     * @return string
     */
    public function getReturnLabelUrl()
    {
      return $this->returnLabelUrl;
    }

    /**
     * @param string $returnLabelUrl
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setReturnLabelUrl($returnLabelUrl)
    {
      $this->returnLabelUrl = $returnLabelUrl;
      return $this;
    }

    /**
     * @return base64Binary
     */
    public function getReturnLabelData()
    {
      return $this->returnLabelData;
    }

    /**
     * @param base64Binary $returnLabelData
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setReturnLabelData($returnLabelData)
    {
      $this->returnLabelData = $returnLabelData;
      return $this;
    }

    /**
     * @return string
     */
    public function getExportLabelUrl()
    {
      return $this->exportLabelUrl;
    }

    /**
     * @param string $exportLabelUrl
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setExportLabelUrl($exportLabelUrl)
    {
      $this->exportLabelUrl = $exportLabelUrl;
      return $this;
    }

    /**
     * @return base64Binary
     */
    public function getExportLabelData()
    {
      return $this->exportLabelData;
    }

    /**
     * @param base64Binary $exportLabelData
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setExportLabelData($exportLabelData)
    {
      $this->exportLabelData = $exportLabelData;
      return $this;
    }

    /**
     * @return string
     */
    public function getCodLabelUrl()
    {
      return $this->codLabelUrl;
    }

    /**
     * @param string $codLabelUrl
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setCodLabelUrl($codLabelUrl)
    {
      $this->codLabelUrl = $codLabelUrl;
      return $this;
    }

    /**
     * @return base64Binary
     */
    public function getCodLabelData()
    {
      return $this->codLabelData;
    }

    /**
     * @param base64Binary $codLabelData
     * @return \Dhl\Versenden\Bcs\Soap\LabelData
     */
    public function setCodLabelData($codLabelData)
    {
      $this->codLabelData = $codLabelData;
      return $this;
    }

}
