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

/**
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging
{
    /**
     * Do customs information have to be added?
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $shipperCountry   = Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $this->getShipment()->getStoreId()
        );
        $recipientCountry = $this->getShipment()->getOrder()->getShippingAddress()->getCountryId();

        // are shipper and receiver located in the same country?
        $sameCountry = parent::displayCustomsValue();

        // are shipper and receiver both located in EU country?
        $bothEu = Mage::helper('core/data')->isCountryInEU($shipperCountry)
            && Mage::helper('core/data')->isCountryInEU($recipientCountry);


        return !($sameCountry || $bothEu);
    }

}
