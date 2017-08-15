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
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Observer_Serialize
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Observer_Serialize extends Dhl_Versenden_Model_Observer_AbstractObserver
{
    /**
     * Convert Info object to serialized representation.
     * - event: model_save_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function serializeVersendenInfo(Varien_Event_Observer $observer)
    {
        $address = $observer->getData('object');
        if (!$address instanceof Mage_Customer_Model_Address_Abstract) {
            return;
        }

        $info = $address->getData('dhl_versenden_info');
        if (!$info || !$info instanceof \Dhl\Versenden\Bcs\Api\Info) {
            return;
        }

        $serializer = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
        $address->setData('dhl_versenden_info', $serializer->serialize($info));
    }

    /**
     * Convert serialized info to Info object.
     * - event: model_load_after
     * - event: model_save_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function unserializeVersendenInfo(Varien_Event_Observer $observer)
    {
        $address = $observer->getData('object');
        if (!$address instanceof Mage_Customer_Model_Address_Abstract) {
            return;
        }

        $info = $address->getData('dhl_versenden_info');
        if (!$info || !is_string($info)) {
            return;
        }

        $serializer = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
        $address->setData('dhl_versenden_info', $serializer->unserialize($info));
    }

    /**
     * Convert serialized info to Info object.
     * - event: sales_order_address_collection_load_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function unserializeVersendenInfoItems(Varien_Event_Observer $observer)
    {
        $collection = $observer->getData('order_address_collection');
        if (!$collection instanceof Mage_Sales_Model_Resource_Order_Address_Collection
            && !$collection instanceof Mage_Sales_Model_Resource_Quote_Address_Collection
        ) {
            return;
        }

        $unserializeInfo = function (Mage_Customer_Model_Address_Abstract $address) {
            $info = $address->getData('dhl_versenden_info');
            if (!$info || !is_string($info)) {
                return;
            }

            $serializer = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
            $address->setData('dhl_versenden_info', $serializer->unserialize($info));
        };

        $collection->walk($unserializeInfo);
    }
}