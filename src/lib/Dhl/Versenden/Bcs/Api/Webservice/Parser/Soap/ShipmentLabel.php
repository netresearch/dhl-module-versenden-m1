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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap;
use \Dhl\Versenden\Bcs\Soap as VersendenApi;
use \Dhl\Versenden\Bcs\Api\Webservice\Parser;
use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;

/**
 * ShipmentLabel
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\Soap
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
abstract class ShipmentLabel extends Shipment implements Parser
{
    /**
     * @param VersendenApi\CreationState $state
     * @return ResponseData\CreateShipment\Label
     */
    protected function parseLabel(VersendenApi\CreationState $state)
    {
        $labelStatus = new ResponseData\Status\Item(
            $state->getSequenceNumber(),
            $state->getLabelData()->getStatus()->getStatusCode(),
            $state->getLabelData()->getStatus()->getStatusText(),
            $state->getLabelData()->getStatus()->getStatusMessage()
        );

        $label = new ResponseData\CreateShipment\Label(
            $labelStatus,
            $state->getSequenceNumber(),
            $state->getLabelData()->getLabelData(),
            $state->getLabelData()->getReturnLabelData(),
            $state->getLabelData()->getExportLabelData(),
            $state->getLabelData()->getCodLabelData()
        );

        return $label;
    }
}
