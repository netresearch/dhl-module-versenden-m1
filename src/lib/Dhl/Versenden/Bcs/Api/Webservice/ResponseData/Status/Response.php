<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status;

class Response
{
    /** @var string */
    protected $statusCode;
    /** @var string */
    protected $statusText;
    /** @var string[] */
    protected $statusMessage;

    /**
     * Status constructor.
     * @param string $statusCode
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
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        return $this->statusText;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return implode(' ', $this->statusMessage);
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return ($this->getStatusCode() == '0');
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return !$this->isSuccess();
    }
}
