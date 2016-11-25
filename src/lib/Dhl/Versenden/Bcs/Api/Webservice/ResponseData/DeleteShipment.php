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
namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData;
use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Response as ResponseStatus;
use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\DeleteShipment\StatusCollection;

/**
 * DeleteShipment
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class DeleteShipment
{
    /** @var ResponseStatus */
    private $status;
    /** @var StatusCollection */
    private $deletedItems;

    /**
     * DeleteShipment constructor.
     * @param ResponseStatus $status
     * @param StatusCollection $deletedItems
     */
    public function __construct(ResponseStatus $status, StatusCollection $deletedItems)
    {
        $this->status = $status;
        $this->deletedItems = $deletedItems;
    }

    /**
     * @return ResponseStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return StatusCollection
     */
    public function getDeletedItems()
    {
        return $this->deletedItems;
    }
}
