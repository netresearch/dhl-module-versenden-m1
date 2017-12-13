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
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Address_Form
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Sales_Order_Address_Form extends Mage_Adminhtml_Block_Sales_Order_Address_Form
{
    /**
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $receiverData
     */
    protected function _prepareAddressFields(
        Varien_Data_Form_Element_Fieldset $fieldset,
        array $receiverData = null
    ) {
        $src = $this->getSkinUrl('images/dhl_versenden/dhl_logo.png');
        $fieldset->addField(
            'versenden_info_street',
            'separator',
            array('value' => '<img src="' . $src . '" alt="DHL Versenden"/>')
        );

        $fieldset->addField(
            'versenden_info_street_name', 'text', array(
                'name'  => "versenden_info[street_name]",
                'label' => $this->__('Street Name'),
                'value' => isset($receiverData['street_name']) ? $receiverData['street_name'] : '',
            )
        );
        $fieldset->addField(
            'versenden_info_street_number', 'text', array(
                'name'  => "versenden_info[street_number]",
                'label' => $this->__('House number'),
                'value' => isset($receiverData['street_number']) ? $receiverData['street_number'] : '',
            )
        );
        $fieldset->addField(
            'versenden_info_address_addition', 'text', array(
                'name'  => "versenden_info[address_addition]",
                'label' => $this->__('Address Addition'),
                'value' => isset($receiverData['address_addition']) ? $receiverData['address_addition'] : '',
            )
        );
    }

    /**
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $receiverData
     */
    protected function _preparePackstationFields(
        Varien_Data_Form_Element_Fieldset $fieldset,
        array $receiverData
    ) {
        $src = $this->getSkinUrl('images/dhl_versenden/icon-packStation.png');
        $fieldset->addField(
            'versenden_info_packstation',
            'separator',
            array('value' => '<img src="' . $src . '" alt="DHL Packstation"/>')
        );

        $fieldset->addField(
            "versenden_info_packstation_number", 'text', array(
                'name'  => "versenden_info[packstation][packstation_number]",
                'label' => $this->__('Packstation Number'),
                'value' => isset($receiverData['packstation_number']) ? $receiverData['packstation_number'] : '',
                'class' => 'validate-number-range number-range-101-999',
            )
        );
        $fieldset->addField(
            "versenden_info_packstation_post_number", 'text', array(
                'name'  => "versenden_info[packstation][post_number]",
                'label' => $this->__('Post Number'),
                'value' => isset($receiverData['post_number']) ? $receiverData['post_number'] : '',
            )
        );
    }

    /**
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $receiverData
     */
    protected function _preparePostfilialeFields(
        Varien_Data_Form_Element_Fieldset $fieldset,
        array $receiverData = null
    ) {
        $src = $this->getSkinUrl('images/dhl_versenden/icon-postOffice.png');
        $fieldset->addField(
            'versenden_info_postfiliale',
            'separator',
            array('value' => '<img src="' . $src . '" alt="DHL Postfiliale"/>')
        );

        $fieldset->addField(
            "versenden_info_postfilial_number", 'text', array(
                'name'  => "versenden_info[postfiliale][postfilial_number]",
                'label' => $this->__('Post Office Number'),
                'value' => isset($receiverData['postfilial_number']) ? $receiverData['postfilial_number'] : '',
                'class' => 'validate-number-range number-range-101-999',
            )
        );
        $fieldset->addField(
            "versenden_info_postfiliale_post_number", 'text', array(
                'name'  => "versenden_info[postfiliale][post_number]",
                'label' => $this->__('Post Number'),
                'value' => isset($receiverData['post_number']) ? $receiverData['post_number'] : '',
            )
        );
    }

    /**
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $receiverData
     */
    protected function _prepareParcelShopFields(Varien_Data_Form_Element_Fieldset $fieldset, array $receiverData)
    {
        $src = $this->getSkinUrl('images/dhl_versenden/icon-parcelShop.png');
        $fieldset->addField(
            'versenden_info_parcel_shop',
            'separator',
            array('value' => '<img src="' . $src . '" alt="DHL Parcelstation"/>')
        );

        $fieldset->addField(
            "versenden_info_parcel_shop_number", 'text', array(
                'name'  => "versenden_info[parcel_shop][parcel_shop_number]",
                'label' => $this->__('Parcelstation Number'),
                'value' => isset($receiverData['parcel_shop_number']) ? $receiverData['parcel_shop_number'] : '',
            )
        );
        $fieldset->addField(
            'versenden_info_parcel_shop_street_name', 'text', array(
                'name'  => "versenden_info[parcel_shop][street_name]",
                'label' => $this->__('Street Name'),
                'value' => isset($receiverData['street_name']) ? $receiverData['street_name'] : '',
            )
        );
        $fieldset->addField(
            'versenden_info_parcel_shop_street_number', 'text', array(
                'name'  => "versenden_info[parcel_shop][street_number]",
                'label' => $this->__('House number'),
                'value' => isset($receiverData['street_number']) ? $receiverData['street_number'] : '',
            )
        );
    }

    /**
     * Define form attributes (id, method, action)
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Billing_Address
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('main');
        $fieldset->addType('separator', 'Dhl_Versenden_Block_Adminhtml_Sales_Order_Address_Element_Separator');

        $address = $this->_getAddress();
        /** @var \Dhl\Versenden\Bcs\Api\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        $receiverData = $versendenInfo->getReceiver()->toArray();
        $this->_prepareAddressFields($fieldset, $receiverData);
        $this->_preparePackstationFields($fieldset, $receiverData['packstation']);
        $this->_preparePostfilialeFields($fieldset, $receiverData['postfiliale']);
        $this->_prepareParcelShopFields($fieldset, $receiverData['parcel_shop']);

        return $this;
    }
}
