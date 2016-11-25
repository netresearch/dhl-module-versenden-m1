<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status;
/**
 * Response
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
