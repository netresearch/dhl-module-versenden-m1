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
 * @package   Netresearch\Dhl\Versenden\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Netresearch\Dhl\Versenden\Webservice\Parser\Soap;
use \Netresearch\Dhl\Bcs\Api as VersendenApi;
use \Netresearch\Dhl\Versenden\Webservice;

/**
 * DeleteShipmentOrder
 *
 * @category Dhl
 * @package  Netresearch\Dhl\Versenden\Webservice\Soap
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class DeleteShipmentOrder extends Shipment implements Webservice\Parser
{
    /**
     * @param VersendenApi\DeleteShipmentOrderResponse $response
     * @return Webservice\ResponseData\DeleteShipment
     */
    public function parse($response)
    {
        $status = $this->parseResponseStatus($response->getStatus());

        // with the SoapClient SOAP_SINGLE_ELEMENT_ARRAYS feature enabled, $deletionStates is always an array
        $deletionStates = $response->getDeletionState();

        $deletedItems = new Webservice\ResponseData\DeleteShipment\StatusCollection();

        /** @var VersendenApi\DeletionState $deletionState */
        foreach ($deletionStates as $deletionState) {
            $deletedItem = $this->parseItemStatus($deletionState->getShipmentNumber(), $deletionState->getStatus());
            $deletedItems->addItem($deletedItem);
        }

        return new Webservice\ResponseData\DeleteShipment($status, $deletedItems);
    }
}

