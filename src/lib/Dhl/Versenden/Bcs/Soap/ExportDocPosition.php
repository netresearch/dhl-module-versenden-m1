<?php

namespace Dhl\Versenden\Bcs\Soap;

class ExportDocPosition
{

    /**
     * @var description $description
     */
    protected $description = null;

    /**
     * @var countryCodeOrigin $countryCodeOrigin
     */
    protected $countryCodeOrigin = null;

    /**
     * @var customsTariffNumber $customsTariffNumber
     */
    protected $customsTariffNumber = null;

    /**
     * @var amount $amount
     */
    protected $amount = null;

    /**
     * @var netWeightInKG $netWeightInKG
     */
    protected $netWeightInKG = null;

    /**
     * @var customsValue $customsValue
     */
    protected $customsValue = null;

    /**
     * @param description $description
     * @param countryCodeOrigin $countryCodeOrigin
     * @param customsTariffNumber $customsTariffNumber
     * @param amount $amount
     * @param netWeightInKG $netWeightInKG
     * @param customsValue $customsValue
     */
    public function __construct($description, $countryCodeOrigin, $customsTariffNumber, $amount, $netWeightInKG, $customsValue)
    {
      $this->description = $description;
      $this->countryCodeOrigin = $countryCodeOrigin;
      $this->customsTariffNumber = $customsTariffNumber;
      $this->amount = $amount;
      $this->netWeightInKG = $netWeightInKG;
      $this->customsValue = $customsValue;
    }

    /**
     * @return description
     */
    public function getDescription()
    {
      return $this->description;
    }

    /**
     * @param description $description
     * @return \Dhl\Versenden\Bcs\Soap\ExportDocPosition
     */
    public function setDescription($description)
    {
      $this->description = $description;
      return $this;
    }

    /**
     * @return countryCodeOrigin
     */
    public function getCountryCodeOrigin()
    {
      return $this->countryCodeOrigin;
    }

    /**
     * @param countryCodeOrigin $countryCodeOrigin
     * @return \Dhl\Versenden\Bcs\Soap\ExportDocPosition
     */
    public function setCountryCodeOrigin($countryCodeOrigin)
    {
      $this->countryCodeOrigin = $countryCodeOrigin;
      return $this;
    }

    /**
     * @return customsTariffNumber
     */
    public function getCustomsTariffNumber()
    {
      return $this->customsTariffNumber;
    }

    /**
     * @param customsTariffNumber $customsTariffNumber
     * @return \Dhl\Versenden\Bcs\Soap\ExportDocPosition
     */
    public function setCustomsTariffNumber($customsTariffNumber)
    {
      $this->customsTariffNumber = $customsTariffNumber;
      return $this;
    }

    /**
     * @return amount
     */
    public function getAmount()
    {
      return $this->amount;
    }

    /**
     * @param amount $amount
     * @return \Dhl\Versenden\Bcs\Soap\ExportDocPosition
     */
    public function setAmount($amount)
    {
      $this->amount = $amount;
      return $this;
    }

    /**
     * @return netWeightInKG
     */
    public function getNetWeightInKG()
    {
      return $this->netWeightInKG;
    }

    /**
     * @param netWeightInKG $netWeightInKG
     * @return \Dhl\Versenden\Bcs\Soap\ExportDocPosition
     */
    public function setNetWeightInKG($netWeightInKG)
    {
      $this->netWeightInKG = $netWeightInKG;
      return $this;
    }

    /**
     * @return customsValue
     */
    public function getCustomsValue()
    {
      return $this->customsValue;
    }

    /**
     * @param customsValue $customsValue
     * @return \Dhl\Versenden\Bcs\Soap\ExportDocPosition
     */
    public function setCustomsValue($customsValue)
    {
      $this->customsValue = $customsValue;
      return $this;
    }

}
