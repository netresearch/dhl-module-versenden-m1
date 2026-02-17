<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Factory for creating webservice builders.
 *
 * Centralizes builder creation logic that was previously duplicated across
 * Autocreate, Client/Shipment, and Carrier/Versenden.
 */
class Dhl_Versenden_Model_Webservice_Builder_Factory
{
    /**
     * Create a fully configured OrderBuilder with all sub-builders.
     *
     * @param float|null $minWeight Minimum package weight in KG (defaults to carrier constant)
     * @return Dhl_Versenden_Model_Webservice_Builder_Order
     */
    public function createOrderBuilder($minWeight = null)
    {
        if ($minWeight === null) {
            $minWeight = Dhl_Versenden_Model_Shipping_Carrier_Versenden::PACKAGE_MIN_WEIGHT;
        }

        $shipperConfig  = Mage::getModel('dhl_versenden/config_shipper');
        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        $serviceConfig  = Mage::getModel('dhl_versenden/config_service');

        $args = ['config' => $shipperConfig];
        $shipperBuilder = Mage::getModel('dhl_versenden/webservice_builder_shipper', $args);

        $args = [
            'country_directory' => Mage::getModel('directory/country'),
            'helper'            => Mage::helper('dhl_versenden/address'),
        ];
        $receiverBuilder = Mage::getModel('dhl_versenden/webservice_builder_receiver', $args);

        $args = [
            'shipper_config'  => $shipperConfig,
            'shipment_config' => $shipmentConfig,
            'service_config'  => $serviceConfig,
        ];
        $serviceBuilder = Mage::getModel('dhl_versenden/webservice_builder_service', $args);

        $args = [
            'unit_of_measure' => 'kg',
            'min_weight'      => $minWeight,
        ];
        $packageBuilder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);
        $customsBuilder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);

        $args = ['config' => $shipmentConfig];
        $settingsBuilder = Mage::getModel('dhl_versenden/webservice_builder_settings', $args);

        $infoBuilder = Mage::getModel('dhl_versenden/info_builder');

        $args = [
            'shipper_builder'  => $shipperBuilder,
            'receiver_builder' => $receiverBuilder,
            'service_builder'  => $serviceBuilder,
            'package_builder'  => $packageBuilder,
            'customs_builder'  => $customsBuilder,
            'settings_builder' => $settingsBuilder,
            'info_builder'     => $infoBuilder,
        ];

        return Mage::getModel('dhl_versenden/webservice_builder_order', $args);
    }

    /**
     * Create a SettingsBuilder.
     *
     * @return Dhl_Versenden_Model_Webservice_Builder_Settings
     */
    public function createSettingsBuilder()
    {
        $config = Mage::getModel('dhl_versenden/config_shipment');
        return Mage::getModel('dhl_versenden/webservice_builder_settings', ['config' => $config]);
    }
}
