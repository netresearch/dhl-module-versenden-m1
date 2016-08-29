<?php

namespace Dhl\Bcs\Api;

class BankCard
{

    /**
     * @var string $CardType
     */
    protected $CardType = null;

    /**
     * @var string $CardNumber
     */
    protected $CardNumber = null;

    /**
     * @var string $BankName
     */
    protected $BankName = null;

    /**
     * @var string $BankCode
     */
    protected $BankCode = null;

    /**
     * @param string $CardType
     * @param string $CardNumber
     * @param string $BankName
     * @param string $BankCode
     */
    public function __construct($CardType, $CardNumber, $BankName, $BankCode)
    {
      $this->CardType = $CardType;
      $this->CardNumber = $CardNumber;
      $this->BankName = $BankName;
      $this->BankCode = $BankCode;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
      return $this->CardType;
    }

    /**
     * @param string $CardType
     * @return \Dhl\Bcs\Api\BankCard
     */
    public function setCardType($CardType)
    {
      $this->CardType = $CardType;
      return $this;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
      return $this->CardNumber;
    }

    /**
     * @param string $CardNumber
     * @return \Dhl\Bcs\Api\BankCard
     */
    public function setCardNumber($CardNumber)
    {
      $this->CardNumber = $CardNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
      return $this->BankName;
    }

    /**
     * @param string $BankName
     * @return \Dhl\Bcs\Api\BankCard
     */
    public function setBankName($BankName)
    {
      $this->BankName = $BankName;
      return $this;
    }

    /**
     * @return string
     */
    public function getBankCode()
    {
      return $this->BankCode;
    }

    /**
     * @param string $BankCode
     * @return \Dhl\Bcs\Api\BankCard
     */
    public function setBankCode($BankCode)
    {
      $this->BankCode = $BankCode;
      return $this;
    }

}
