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
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . '/Sales/OrderController.php';
/**
 * Dhl_Versenden_Adminhtml_Sales_Order_ShipmentController
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Adminhtml_Sales_OrderController
    extends Mage_Adminhtml_Sales_OrderController
{
    /**
     * Update Versenden Info using order address and additional POST data from form.
     *
     * @param Mage_Sales_Model_Order_Address $address
     * @param array $data
     */
    protected function _addVersendenData(Mage_Sales_Model_Order_Address $address, array $data)
    {
        /** @var \Dhl\Versenden\Bcs\Api\Info $origInfo */
        $origInfo = $address->getData('dhl_versenden_info');
        if (!$origInfo instanceof Dhl\Versenden\Bcs\Api\Info) {
            return;
        }

        $services = $origInfo->getServices()->toArray();

        $infoBuilder = Mage::getModel('dhl_versenden/info_builder');
        $serviceInfo = array(
            'shipment_service' => array(),
            'service_setting' => array()
        );

        // rebuild Versenden Info from address
        $versendenInfo = $infoBuilder->infoFromSales($address, $serviceInfo, $address->getOrder()->getStoreId());

        // set previously selected services
        $versendenInfo->getServices()->fromArray($services);

        // update street using POST data.
        $versendenData = (isset($data['versenden_info']) && $data['versenden_info'])
            ? $data['versenden_info']
            : array();
        if (isset($versendenData['street_name']) && isset($versendenData['street_number'])
            && isset($versendenData['address_addition'])) {
            $versendenInfo->getReceiver()->streetName = $versendenData['street_name'];
            $versendenInfo->getReceiver()->streetNumber = $versendenData['street_number'];
            $versendenInfo->getReceiver()->addressAddition = $versendenData['address_addition'];
        }

        $packstationData = (isset($versendenData['packstation']) && $versendenData['packstation'])
            ? $versendenData['packstation']
            : array();
        if (isset($packstationData['packstation_number']) && $packstationData['packstation_number']
            && isset($packstationData['post_number']) && $packstationData['post_number']) {
            // update postal facility using POST data.
            $versendenInfo->getReceiver()->getPackstation()->zip = $versendenInfo->getReceiver()->zip;
            $versendenInfo->getReceiver()->getPackstation()->city = $versendenInfo->getReceiver()->city;
            $versendenInfo->getReceiver()->getPackstation()->country = $versendenInfo->getReceiver()->country;
            $versendenInfo->getReceiver()->getPackstation()->countryISOCode =
                $versendenInfo->getReceiver()->countryISOCode;
            $versendenInfo->getReceiver()->getPackstation()->packstationNumber = $packstationData['packstation_number'];
            $versendenInfo->getReceiver()->getPackstation()->postNumber = $packstationData['post_number'];
        } else {
            // otherwise clear
            $versendenInfo->getReceiver()->getPackstation()->fromArray(
                array(
                    'zip' => null,
                    'city' => null,
                    'country' => null,
                    'country_iso_code' => null,
                    'packstation_number' => null,
                    'post_number' => null,
                )
            );
        }

        $postfilialeData = (isset($versendenData['postfiliale']) && $versendenData['postfiliale'])
            ? $versendenData['postfiliale']
            : array();
        if (isset($postfilialeData['postfilial_number']) && $postfilialeData['postfilial_number']
            && isset($postfilialeData['post_number']) && $postfilialeData['post_number']) {
            // update postal facility using POST data.
            $versendenInfo->getReceiver()->getPostfiliale()->zip = $versendenInfo->getReceiver()->zip;
            $versendenInfo->getReceiver()->getPostfiliale()->city = $versendenInfo->getReceiver()->city;
            $versendenInfo->getReceiver()->getPostfiliale()->country = $versendenInfo->getReceiver()->country;
            $versendenInfo->getReceiver()->getPostfiliale()->countryISOCode =
                $versendenInfo->getReceiver()->countryISOCode;
            $versendenInfo->getReceiver()->getPostfiliale()->postfilialNumber = $postfilialeData['postfilial_number'];
            $versendenInfo->getReceiver()->getPostfiliale()->postNumber = $postfilialeData['post_number'];
        } else {
            // otherwise clear
            $versendenInfo->getReceiver()->getPostfiliale()->fromArray(
                array(
                    'zip' => null,
                    'city' => null,
                    'country' => null,
                    'country_iso_code' => null,
                    'postfilial_number' => null,
                    'post_number' => null,
                )
            );
        }

        $parcelShopData = (isset($versendenData['parcel_shop']) && $versendenData['parcel_shop'])
            ? $versendenData['parcel_shop']
            : array();
        if (isset($parcelShopData['parcel_shop_number']) && $parcelShopData['parcel_shop_number']
            && isset($parcelShopData['street_name']) && $parcelShopData['street_name']
            && isset($parcelShopData['street_number']) && $parcelShopData['street_number']) {
            // update postal facility using POST data.
            $versendenInfo->getReceiver()->getParcelShop()->zip = $versendenInfo->getReceiver()->zip;
            $versendenInfo->getReceiver()->getParcelShop()->city = $versendenInfo->getReceiver()->city;
            $versendenInfo->getReceiver()->getParcelShop()->country = $versendenInfo->getReceiver()->country;
            $versendenInfo->getReceiver()->getParcelShop()->countryISOCode =
                $versendenInfo->getReceiver()->countryISOCode;
            $versendenInfo->getReceiver()->getParcelShop()->parcelShopNumber = $parcelShopData['parcel_shop_number'];
            $versendenInfo->getReceiver()->getParcelShop()->streetName = $parcelShopData['street_name'];
            $versendenInfo->getReceiver()->getParcelShop()->streetNumber = $parcelShopData['street_number'];
        } else {
            // otherwise clear
            $versendenInfo->getReceiver()->getParcelShop()->fromArray(
                array(
                    'zip' => null,
                    'city' => null,
                    'country' => null,
                    'country_iso_code' => null,
                    'parcel_shop_number' => null,
                    'street_name' => null,
                    'street_number' => null,
                )
            );
        }

        $address->setData('dhl_versenden_info', $versendenInfo);
    }

    /**
     * Announce that the shipping address has changed.
     *
     * @param Mage_Sales_Model_Order_Address $address
     */
    protected function _dispatchVersendenData(Mage_Sales_Model_Order_Address $address)
    {
        $versendenInfo = $address->getData('dhl_versenden_info');
        if (!$versendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            return;
        }

        $facility = new Varien_Object();
        if ($versendenInfo->getReceiver()->getPackstation()->packstationNumber) {
            $packstation = $versendenInfo->getReceiver()->getPackstation();
            $facility->setData(
                array(
                    'shop_type'   => \Dhl\Versenden\Bcs\Api\Info\Receiver\PostalFacility::TYPE_PACKSTATION,
                    'shop_number' => $packstation->packstationNumber,
                    'post_number' => $packstation->postNumber,
                )
            );
        }

        if ($versendenInfo->getReceiver()->getPostfiliale()->postfilialNumber) {
            $postfiliale = $versendenInfo->getReceiver()->getPostfiliale();
            $facility->setData(
                array(
                    'shop_type'   => \Dhl\Versenden\Bcs\Api\Info\Receiver\PostalFacility::TYPE_POSTFILIALE,
                    'shop_number' => $postfiliale->postfilialNumber,
                    'post_number' => $postfiliale->postNumber,
                )
            );
        }

        if ($versendenInfo->getReceiver()->getParcelShop()->parcelShopNumber) {
            $parcelShop = $versendenInfo->getReceiver()->getParcelShop();
            $facility->setData(
                array(
                    'shop_type'   => \Dhl\Versenden\Bcs\Api\Info\Receiver\PostalFacility::TYPE_POSTFILIALE,
                    'shop_number' => $parcelShop->parcelShopNumber,
                )
            );
        }

        $eventData = array(
            'customer_address' => $address,
            'postal_facility' => $facility,
        );
        Mage::dispatchEvent('dhl_versenden_announce_postal_facility', $eventData);
    }

    /**
     * Save order address
     */
    public function addressSaveAction()
    {
        $addressId  = $this->getRequest()->getParam('address_id');
        $address    = Mage::getModel('sales/order_address')->load($addressId);
        $data       = $this->getRequest()->getPost();
        if ($data && $address->getId()) {
            $address->addData($data);

            try {
                $address->implodeStreetAddress();
                $this->_addVersendenData($address, $data);
                $address->save();

                // dispatch versenden info update success
                $this->_dispatchVersendenData($address);

                $this->_getSession()->addSuccess(Mage::helper('sales')->__('The order address has been updated.'));
                $this->_redirect('*/*/view', array('order_id'=>$address->getParentId()));
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('sales')->__(
                        'An error occurred while updating the order address. The address has not been changed.'
                    )
                );
            }

            $this->_redirect('*/*/address', array('address_id'=>$address->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }
}
