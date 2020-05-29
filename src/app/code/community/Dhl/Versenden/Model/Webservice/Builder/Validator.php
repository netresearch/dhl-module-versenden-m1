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
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

use Dhl\Versenden\Bcs\Api\Product;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData\ValidationException;

/**
 * Dhl_Versenden_Model_Webservice_Builder_Order
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Webservice_Builder_Validator
{
    /**
     * @param Mage_Shipping_Model_Shipment_Request $shipmentRequest
     * @param ShipmentOrder $shipmentOrder
     * @throws ValidationException
     */
    public function validate(\Mage_Shipping_Model_Shipment_Request $shipmentRequest, ShipmentOrder $shipmentOrder)
    {
        $orderShipment = $shipmentRequest->getOrderShipment();

        $shippingProduct = $shipmentRequest->getData('gk_api_product');

        $insurance = $shipmentOrder->getServiceSelection()->getInsurance();
        $bulkyGoods = $shipmentOrder->getServiceSelection()->isBulkyGoods();
        $cod = $shipmentOrder->getServiceSelection()->getCod();
        $preferredDay = $shipmentOrder->getServiceSelection()->getPreferredDay();
        $visualCheckOfAge = $shipmentOrder->getServiceSelection()->getVisualCheckOfAge();

        $canShipPartially = empty($insurance) && empty($cod);
        $isPartial = ($orderShipment->getOrder()->getTotalQtyOrdered() != $orderShipment->getTotalQty());

        if (!$canShipPartially && $isPartial) {
            $message = 'Cannot do partial shipment with COD or Additional Insurance.';
            throw new ValidationException($message);
        }

        $canUseMerchandiseShipment = empty($insurance)
            && empty($bulkyGoods)
            && empty($cod)
            && empty($preferredDay)
            && empty($visualCheckOfAge);
        $isMerchandiseShipment = ($shippingProduct == Product::CODE_WARENPOST_NATIONAL);

        if (!$canUseMerchandiseShipment && $isMerchandiseShipment) {
            $message = 'Warenpost cannot be booked with the services '
                . 'Additional Insurance, Bulky Goods, Cash on Delivery, Preferred Day, Visual Check of Age.';
            throw new ValidationException($message);
        }
    }
}
