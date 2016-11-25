<?php

namespace Dhl\Versenden\Bcs\Soap;

class Statusinformation
{

    /**
     * @var int $statusCode
     */
    protected $statusCode = null;

    /**
     * @var string $statusText
     */
    protected $statusText = null;

    /**
     * @var string[] $statusMessage
     */
    protected $statusMessage = null;

    /**
     * @param int $statusCode
     * @param string $statusText
     * @param string[] $statusMessage
     */
    public function __construct($statusCode, $statusText, array $statusMessage)
    {
      $this->statusCode = $statusCode;
      $this->statusText = $statusText;
      $this->statusMessage = $statusMessage;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
      return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return \Dhl\Versenden\Bcs\Soap\Statusinformation
     */
    public function setStatusCode($statusCode)
    {
      $this->statusCode = $statusCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
      return $this->statusText;
    }

    /**
     * @param string $statusText
     * @return \Dhl\Versenden\Bcs\Soap\Statusinformation
     */
    public function setStatusText($statusText)
    {
      $this->statusText = $statusText;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getStatusMessage()
    {
      return $this->statusMessage;
    }

    /**
     * @param string[] $statusMessage
     * @return \Dhl\Versenden\Bcs\Soap\Statusinformation
     */
    public function setStatusMessage(array $statusMessage)
    {
      $this->statusMessage = $statusMessage;
      return $this;
    }

}
