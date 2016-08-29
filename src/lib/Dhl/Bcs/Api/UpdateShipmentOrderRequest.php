<?php

namespace Dhl\Bcs\Api;

class UpdateShipmentOrderRequest
{

    /**
     * @var Version $Version
     */
    protected $Version = null;

    /**
     * @var shipmentNumber $shipmentNumber
     */
    protected $shipmentNumber = null;

    /**
     * @var ShipmentOrderType $ShipmentOrder
     */
    protected $ShipmentOrder = null;

    /**
     * @param Version $Version
     * @param shipmentNumber $shipmentNumber
     * @param ShipmentOrderType $ShipmentOrder
     */
    public function __construct($Version, $shipmentNumber, $ShipmentOrder)
    {
      $this->Version = $Version;
      $this->shipmentNumber = $shipmentNumber;
      $this->ShipmentOrder = $ShipmentOrder;
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
     * @return \Dhl\Bcs\Api\UpdateShipmentOrderRequest
     */
    public function setVersion($Version)
    {
      $this->Version = $Version;
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
     * @return \Dhl\Bcs\Api\UpdateShipmentOrderRequest
     */
    public function setShipmentNumber($shipmentNumber)
    {
      $this->shipmentNumber = $shipmentNumber;
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
     * @return \Dhl\Bcs\Api\UpdateShipmentOrderRequest
     */
    public function setShipmentOrder($ShipmentOrder)
    {
      $this->ShipmentOrder = $ShipmentOrder;
      return $this;
    }

}
