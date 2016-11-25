<?php

namespace Dhl\Versenden\Bcs\Soap;

class ShipmentDetailsType
{

    /**
     * @var string $product
     */
    protected $product = null;

    /**
     * @var accountNumber $accountNumber
     */
    protected $accountNumber = null;

    /**
     * @var customerReference $customerReference
     */
    protected $customerReference = null;

    /**
     * @var shipmentDate $shipmentDate
     */
    protected $shipmentDate = null;

    /**
     * @var returnShipmentAccountNumber $returnShipmentAccountNumber
     */
    protected $returnShipmentAccountNumber = null;

    /**
     * @var returnShipmentReference $returnShipmentReference
     */
    protected $returnShipmentReference = null;

    /**
     * @param string $product
     * @param accountNumber $accountNumber
     * @param shipmentDate $shipmentDate
     */
    public function __construct($product, $accountNumber, $shipmentDate)
    {
      $this->product = $product;
      $this->accountNumber = $accountNumber;
      $this->shipmentDate = $shipmentDate;
    }

    /**
     * @return string
     */
    public function getProduct()
    {
      return $this->product;
    }

    /**
     * @param string $product
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentDetailsType
     */
    public function setProduct($product)
    {
      $this->product = $product;
      return $this;
    }

    /**
     * @return accountNumber
     */
    public function getAccountNumber()
    {
      return $this->accountNumber;
    }

    /**
     * @param accountNumber $accountNumber
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentDetailsType
     */
    public function setAccountNumber($accountNumber)
    {
      $this->accountNumber = $accountNumber;
      return $this;
    }

    /**
     * @return customerReference
     */
    public function getCustomerReference()
    {
      return $this->customerReference;
    }

    /**
     * @param customerReference $customerReference
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentDetailsType
     */
    public function setCustomerReference($customerReference)
    {
      $this->customerReference = $customerReference;
      return $this;
    }

    /**
     * @return shipmentDate
     */
    public function getShipmentDate()
    {
      return $this->shipmentDate;
    }

    /**
     * @param shipmentDate $shipmentDate
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentDetailsType
     */
    public function setShipmentDate($shipmentDate)
    {
      $this->shipmentDate = $shipmentDate;
      return $this;
    }

    /**
     * @return returnShipmentAccountNumber
     */
    public function getReturnShipmentAccountNumber()
    {
      return $this->returnShipmentAccountNumber;
    }

    /**
     * @param returnShipmentAccountNumber $returnShipmentAccountNumber
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentDetailsType
     */
    public function setReturnShipmentAccountNumber($returnShipmentAccountNumber)
    {
      $this->returnShipmentAccountNumber = $returnShipmentAccountNumber;
      return $this;
    }

    /**
     * @return returnShipmentReference
     */
    public function getReturnShipmentReference()
    {
      return $this->returnShipmentReference;
    }

    /**
     * @param returnShipmentReference $returnShipmentReference
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentDetailsType
     */
    public function setReturnShipmentReference($returnShipmentReference)
    {
      $this->returnShipmentReference = $returnShipmentReference;
      return $this;
    }

}
