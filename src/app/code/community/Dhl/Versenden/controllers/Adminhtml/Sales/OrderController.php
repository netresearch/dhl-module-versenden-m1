<?php

/**
 * See LICENSE.md for license details.
 */

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . '/Sales/OrderController.php';

class Dhl_Versenden_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    /**
     * Update Versenden Info using order address and additional POST data from form.
     *
     * @param Mage_Sales_Model_Order_Address $address
     * @param array $data
     */
    protected function _addVersendenData(Mage_Sales_Model_Order_Address $address, array $data)
    {
        /** @var \Dhl\Versenden\ParcelDe\Info $origInfo */
        $origInfo = $address->getData('dhl_versenden_info');
        if (!$origInfo instanceof Dhl\Versenden\ParcelDe\Info) {
            return;
        }

        $services = $origInfo->getServices()->toArray();

        $infoBuilder = Mage::getModel('dhl_versenden/info_builder');
        $serviceInfo = [
            'shipment_service' => [],
            'service_setting' => [],
        ];

        // rebuild Versenden Info from address
        $versendenInfo = $infoBuilder->infoFromSales($address, $serviceInfo, $address->getOrder()->getStoreId());

        // set previously selected services
        $versendenInfo->getServices()->fromArray($services);

        // update street using POST data.
        $versendenData = (isset($data['versenden_info']) && $data['versenden_info'])
            ? $data['versenden_info']
            : [];
        if (isset($versendenData['street_name']) && isset($versendenData['street_number'])
            && isset($versendenData['address_addition'])) {
            $versendenInfo->getReceiver()->streetName = $versendenData['street_name'];
            $versendenInfo->getReceiver()->streetNumber = $versendenData['street_number'];
            $versendenInfo->getReceiver()->addressAddition = $versendenData['address_addition'];
        }

        $packstationData = (isset($versendenData['packstation']) && $versendenData['packstation'])
            ? $versendenData['packstation']
            : [];
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
                [
                    'zip' => null,
                    'city' => null,
                    'country' => null,
                    'country_iso_code' => null,
                    'packstation_number' => null,
                    'post_number' => null,
                ],
            );
        }

        $postfilialeData = (isset($versendenData['postfiliale']) && $versendenData['postfiliale'])
            ? $versendenData['postfiliale']
            : [];
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
                [
                    'zip' => null,
                    'city' => null,
                    'country' => null,
                    'country_iso_code' => null,
                    'postfilial_number' => null,
                    'post_number' => null,
                ],
            );
        }

        $parcelShopData = (isset($versendenData['parcel_shop']) && $versendenData['parcel_shop'])
            ? $versendenData['parcel_shop']
            : [];
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
                [
                    'zip' => null,
                    'city' => null,
                    'country' => null,
                    'country_iso_code' => null,
                    'parcel_shop_number' => null,
                    'street_name' => null,
                    'street_number' => null,
                ],
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
        if (!$versendenInfo instanceof \Dhl\Versenden\ParcelDe\Info) {
            return;
        }

        $facility = new Varien_Object();
        if ($versendenInfo->getReceiver()->getPackstation()->packstationNumber) {
            $packstation = $versendenInfo->getReceiver()->getPackstation();
            $facility->setData(
                [
                    'shop_type'   => \Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility::TYPE_PACKSTATION,
                    'shop_number' => $packstation->packstationNumber,
                    'post_number' => $packstation->postNumber,
                ],
            );
        }

        if ($versendenInfo->getReceiver()->getPostfiliale()->postfilialNumber) {
            $postfiliale = $versendenInfo->getReceiver()->getPostfiliale();
            $facility->setData(
                [
                    'shop_type'   => \Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility::TYPE_POSTFILIALE,
                    'shop_number' => $postfiliale->postfilialNumber,
                    'post_number' => $postfiliale->postNumber,
                ],
            );
        }

        if ($versendenInfo->getReceiver()->getParcelShop()->parcelShopNumber) {
            $parcelShop = $versendenInfo->getReceiver()->getParcelShop();
            $facility->setData(
                [
                    'shop_type'   => \Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility::TYPE_POSTFILIALE,
                    'shop_number' => $parcelShop->parcelShopNumber,
                ],
            );
        }

        $eventData = [
            'customer_address' => $address,
            'postal_facility' => $facility,
        ];
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
                $this->_redirect('*/*/view', ['order_id' => $address->getParentId()]);
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('sales')->__(
                        'An error occurred while updating the order address. The address has not been changed.',
                    ),
                );
            }

            $this->_redirect('*/*/address', ['address_id' => $address->getId()]);
        } else {
            $this->_redirect('*/*/');
        }
    }
}
