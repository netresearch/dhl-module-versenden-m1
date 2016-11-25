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
use \Dhl\Versenden\Bcs\Api\Shipment\Service\Type as Service;
/**
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
abstract class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging
{
    /**
     * Check if services need to be rendered at all.
     *
     * @return string
     */
    public function renderView()
    {
        $shippingMethod = $this->getShipment()->getOrder()->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE) {
            return '';
        }

        return parent::renderView();
    }

    /**
     * @return \Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic[]
     */
    abstract public function getServices();

    /**
     * @param Service\Generic $service
     * @return Service\Renderer
     */
    abstract public function getRenderer(Service\Generic $service);
}
